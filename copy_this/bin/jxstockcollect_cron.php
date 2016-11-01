<?php
/*
 *    This file is part of the module jxStockCollect for OXID eShop Community Edition.
 *
 *    The module jxStockCollect for OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    The module jxStockCollect for OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      https://github.com/job963/jxStockCollect
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @copyright (C) Joachim Barthel 2016
 * 
 */

class jxStockCollectCron
{
    protected $dbh;
    protected $updated;

    public function __construct()
    {
        require_once '../config.inc.php';
            
        $dbConn = 'mysql:host='.$this->dbHost.';dbname='.$this->dbName;
        $dbUser = $this->dbUser;
        $dbPass = $this->dbPwd;        
        $this->dbh = new PDO($dbConn, $dbUser, $dbPass); 
        $this->dbh->exec('set names "utf8"');
    }
    
    
    public function __destruct() 
    {
        $this->dbh = NULL;
    }

    public function updateStockValues()
    {
        $sSql = "SELECT u.jxurl, p.jxpattern, p.jxavailabletext, p.jxlowstocktext, p.jxoutofstocktext, u.jxartnum, u.jxstock "
                . "FROM jxstockcollecturls u, jxstockcollectpatterns p "
                . "WHERE u.jxpatterntype = p.jxpatterntype "
                    . "AND u.jxactive = 1 "
                    . "AND u.jxdeactivation = '0000-00-00 00:00:00' "
                . "ORDER BY u.jxpatterntype, u.jxartnum ";
        
        $stmt = $this->dbh->prepare($sSql);
        $stmt->execute();
        $aProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ( count($aProducts) == 0 )
            echo 'no products - nothing to do';
        
        foreach ($aProducts as $key => $aProduct) {
            $aCollectParams = array(
                'url'        => $aProduct['jxurl'],
                'pattern'    => $aProduct['jxpattern'],
                'available'  => $aProduct['jxavailabletext'],
                'lowstock'   => $aProduct['jxlowstocktext'],
                'outofstock' => $aProduct['jxoutofstocktext'],
                'artnum'     => $aProduct['jxartnum']
            );
            $this->updated = 0;
            $stockValue = $this->_collectStockValue($aCollectParams);
            echo "\n".$aProduct['jxartnum']." - DelivererStock=" . $stockValue ."\n";
            
            if ($stockValue > 0) {
                if (($this->_isInstalled('jxinvarticles')) and ($this->_getInventoryStock($aProduct['jxartnum']) > 0)) {
                    $stockValue = $this->_getInventoryStock($aProduct['jxartnum']);
                }
                elseif ($stockValue > 1) {
                    $stockValue = $aProduct['jxstock'];
                }
                else {
                    $stockValue = 1;
                }
            }
            
            // Product is available at the vendor
            if ($stockValue >= 0) {
                $sSql = "UPDATE oxarticles SET oxstock={$stockValue}, oxtimestamp = NOW() WHERE oxartnum = '{$aProduct['jxartnum']}' ";
                try {
                    $stmt = $this->dbh->prepare($sSql);
                    $stmt->execute();
                    $this->updated = $stmt->rowCount();
echo $sSql. ' ('.$this->updated.')'."\n";
        
                    // store update status
                    $sSql = "UPDATE jxstockcollecturls SET jxartupdated = {$this->updated}, jxtimestamp = NOW() WHERE jxartnum = '{$aProduct['jxartnum']}' ";
                    $stmt = $this->dbh->prepare($sSql);
                    $stmt->execute();
echo $sSql.' ['.$stmt->rowCount().']'."\n";
                }
                catch (Exception $e) {
                    echo 'SQL-Error '.$e->getCode().' in SQL statement'."\n".$e->getMessage()."\n";
                }
            }
            
            // Product is available in the own inventory
            if ($stockValue == -2) {
                if ($this->_isInstalled('jxinvarticles')) {
                    // jxInventory is installed
                    if ( $this->_getInventoryStock($aProduct['jxartnum']) == 0 ) {
                        $this->_deactivateProduct($aProduct['jxartnum']);
                        echo "Artikel {$aProduct['jxartnum']} ist nicht mehr verfuegbar und wurde deaktiviert\n";
                    }
                    else {
                        echo "Inventar=".$this->_getInventoryStock($aProduct['jxartnum'])."\n";
                        $this->updated = -1;
                        $sSql = "UPDATE jxstockcollecturls SET jxartupdated = {$this->updated}, jxtimestamp = NOW() WHERE jxartnum = '{$aProduct['jxartnum']}' ";
                        $stmt = $this->dbh->prepare($sSql);
                        $stmt->execute();
                    }
                }
                else {
                    $this->_deactivateProduct($aProduct['jxartnum']);
                    echo "Artikel {$aProduct['jxartnum']} ist nicht mehr verfuegbar und wurde deaktiviert\n";
                }
            }
            
            $this->_updateVarStock($aProduct['jxartnum']);
            
        } // foreach

    }


    /*
     * Collect the stock state from deliverers website
     * 
     * Returns
     *   1 = available / deliverable
     *   0 = not deliverable yet
     *  -1 = error on calling the page
     *  -2 = out of stock
     * 
     */
    private function _collectStockValue($aCollectParams)
    {
        //$timeBefore = microtime(true);
        $ch = curl_init();
        //echo $url.'<br/>';
        curl_setopt($ch, CURLOPT_URL, $aCollectParams['url']);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        //echo curl_errno($ch);
        if (empty($result)) {
            echo "\n" . $aCollectParams['url'] . "\n".'cURL-Fehler: ' . curl_errno($ch) . ' - ' . curl_error($ch);
            return -1;
        }
        if (curl_errno($ch) != 0) {
            echo "\n" . $aCollectParams['url'] . "\n".'cURL-Fehler: ' . curl_errno($ch) . ' - ' . curl_error($ch);
            return -1;
        }
        //$info = curl_getinfo($ch);
        $info = curl_getinfo($ch);
        if ($info['redirect_count'] > 0) {
            echo "redir: ".$info['url']."\n";
            $info['http_code'] = "301";
        }
        else {
            $info['url'] = '';
        }
        
        // save the returned http code
        $sSql = "UPDATE jxstockcollecturls SET jxhttpcode = '{$info['http_code']}', jxredirurl = '{$info['url']}', jxtimestamp = NOW() WHERE jxartnum = '{$aCollectParams['artnum']}' ";
echo "\n".$sSql;
        $stmt = $this->dbh->prepare($sSql);
        $stmt->execute();
echo " (".$stmt->rowCount().")";
        
        if ($info['http_code'] != '200') {
            print_r($info);
            return -1;
        }
        echo "\n".$aCollectParams['url'];
        echo "\n".'Es wurden ' . $info['total_time'] . ' Sekunden benoetigt';
        curl_close ($ch);
        
        //echo "\n"."preg_match({$aCollectParams['pattern']}, result, matches)";
        preg_match($aCollectParams['pattern'], $result, $matches);

        if (empty($matches)) {
            echo "\n".'matches ist leer / nicht gefunden';
            return -1;
        }
        
        if (strpos($matches[1], $aCollectParams['available']) !== false) {
            // available
            return 2;
        }
        
        if (strpos($matches[1], $aCollectParams['lowstock']) !== false) {
            // low stock
            return 1;
        }
        
        if (strpos($matches[1], $aCollectParams['outofstock']) !== false) {
            // out of stock
            return -2;
        } 

        // no info found
        return 0;
        
    }


    private function _updateVarStock($sArtnum)
    {
        $sSql = "SELECT oxparentid FROM oxarticles WHERE oxartnum = '{$sArtnum}' AND oxparentid != '' ";
        
        $stmt = $this->dbh->prepare($sSql);
        $stmt->execute();
        $aParentIds = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($aParentIds as $key => $sParentId) {
            //print_r($sParentId);
            $sSql = "SELECT SUM(oxstock) AS oxstocksum FROM oxarticles WHERE oxparentid = '{$sParentId['oxparentid']}' ";
            $stmt = $this->dbh->prepare($sSql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sSql = "UPDATE oxvarstock SET oxvarstock = {$result['oxstocksum']}, oxtimestamp = NOW() WHERE oxid = '{$sParentId['oxparentid']}' ";
            $stmt = $this->dbh->prepare($sSql);
            $stmt->execute();
        }
    }


    private function _deactivateProduct($sArtnum)
    {
        //deactivate product
        $sSql = "UPDATE oxarticles SET oxactive = 0, oxstock = 0, oxtimestamp = NOW() WHERE oxartnum = '{$sArtnum}' ";
        $stmt = $this->dbh->prepare($sSql);
        $stmt->execute();
        $this->updated = $stmt->rowCount();
        
        // mark product as deactivated
        $sSql = "UPDATE jxstockcollecturls SET jxdeactivation = NOW(), jxartupdated = {$this->updated}, jxtimestamp = NOW() WHERE jxartnum = '{$sArtnum}' ";
        $stmt = $this->dbh->prepare($sSql);
        $stmt->execute();
    }


    private function _getInventoryStock($sArtnum)
    {
        $sSql = "SELECT i.jxinvstock "
                . "FROM jxinvarticles i, oxarticles a "
                . "WHERE i.jxartid = a.oxid "
                    . "AND a.oxartnum = '{$sArtnum}'";
        
        $stmt = $this->dbh->prepare($sSql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ( $result ) {
            return $result['jxinvstock'];
        }
        else {
            return 0;
        }
    }


    private function _isInstalled($sTable)
    {
        $stmt = $this->dbh->prepare( "SHOW TABLES LIKE '{$sTable}'" );
        $stmt->execute();
        if ( $stmt->fetch(PDO::FETCH_ASSOC) ) {
            return true;
        }
        else {
            return false;
        }
    }


    private function _logAction($value)
    {
        $nPathEnd = strpos( $_SERVER['SCRIPT_FILENAME'], '/modules' );
        $sShopPath = substr( $_SERVER['SCRIPT_FILENAME'], 0, $nPathEnd );
        $sLogPath = $sShopPath.'/log/';
        
        $fh = fopen($sLogPath.'jxmods.log',"a+");
        
        if (gettype($value) == 'array' OR gettype($value) == 'object')
            fputs( $fh, print_r($value, TRUE) );
        else
            fputs( $fh, date("Y-m-d H:i:s  ") . $value . "\n" );
        
        fclose($fh);
    }
    
} 

$jxCron = new jxStockCollectCron();
$jxCron->updateStockValues();
