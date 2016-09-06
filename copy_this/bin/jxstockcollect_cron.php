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
        $sSql = "SELECT u.jxurl, p.jxpattern, p.jxavailabletext, p.jxoutofstocktext, u.jxartnum, u.jxstock "
                . "FROM jxstockcollecturls u, jxstockcollectpatterns p "
                . "WHERE u.jxpatterntype = p.jxpatterntype "
                    . "AND u.jxactive = 1 "
                    . "AND u.jxdeactivation = '0000-00-00 00:00:00' ";
        
        $stmt = $this->dbh->prepare($sSql);
        $stmt->execute();
        $aProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ( count($aProducts) == 0 )
            echo 'nothing to do';
        
        foreach ($aProducts as $key => $aProduct) {
            $aCollectParams = array(
                'url'        => $aProduct['jxurl'],
                'pattern'    => $aProduct['jxpattern'],
                'available'  => $aProduct['jxavailabletext'],
                'outofstock' => $aProduct['jxoutofstocktext']
            );
            $stockValue = $this->_collectStockValue($aCollectParams);
            echo "\n".$aProduct['jxartnum']." - DelivererStock=" . $stockValue ."\n";
            
            if ($stockValue > 0) {
                if (($this->_isInstalled('jxinvarticles')) and ($this->_getInventoryStock($aProduct['jxartnum']) > 0)) {
                    $stockValue = $this->_getInventoryStock($aProduct['jxartnum']);
                }
                else {
                    $stockValue = $aProduct['jxstock'];
                }
            }
            
            if ($stockValue >= 0) {
                $sSql = "UPDATE oxarticles SET oxstock={$stockValue} WHERE oxartnum = '{$aProduct['jxartnum']}' ";
                try {
                    $stmt = $this->dbh->prepare($sSql);
                    $stmt->execute();
                }
                catch (Exception $e) {
                    echo 'SQL-Error '.$e->getCode().' in SQL statement'."\n".$e->getMessage()."\n";
                }
            }
            
            if ($stockValue == -2) {
                if ($this->_isInstalled('jxinvarticles')) {
                    // jxInventory is installed
                    if ( $this->_getInventoryStock($aProduct['jxartnum']) == 0 ) {
                        $this->_deactivateProduct($aProduct['jxartnum']);
                        echo "Artikel {$aProduct['jxartnum']} ist nicht mehr verfuegbar und wurde deaktiviert\n";
                    }
                    else {
                        echo "Inventar=".$this->_getInventoryStock($aProduct['jxartnum'])."\n";
                    }
                }
                else {
                    $this->_deactivateProduct($aProduct['jxartnum']);
                    echo "Artikel {$aProduct['jxartnum']} ist nicht mehr verfuegbar und wurde deaktiviert\n";
                }
            }
            
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
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        //echo curl_errno($ch);
        if (empty($result)) {
            echo 'cURL-Fehler: ' . curl_errno($ch) . ' - ' . curl_error($ch);
            return -1;
        }
        if (curl_errno($ch) != 0) {
            echo 'cURL-Fehler: ' . curl_errno($ch) . ' - ' . curl_error($ch);
            return -1;
        }
        //$info = curl_getinfo($ch);
        $info = curl_getinfo($ch);
        if ($info['http_code'] != '200') {
            print_r($info);
            return -1;
        }
        echo "\n".$aCollectParams['url'];
        echo "\n".'Es wurden ' . $info['total_time'] . ' Sekunden benoetigt';
        curl_close ($ch);
        
        //echo "\n"."preg_match({$aCollectParams['pattern']}, result, matches)";
        preg_match($aCollectParams['pattern'], $result, $matches);
        //echo (microtime(true)-$timeBefore) . "\n";
        //--echo $matches[1];
        if (empty($matches)) {
            echo "\n".'matches ist leer';
            return -1;
        }
        
        //if (strpos($matches[1], 'class="status available"') !== false) {
        if (strpos($matches[1], $aCollectParams['available']) !== false) {
            return 1;
        }
        else {
            if (strpos($matches[1], $aCollectParams['outofstock']) !== false) {
                return -2;
            } 
            else {
                return 0;
            }
        }
        
    }


    private function _deactivateProduct($sArtnum)
    {
        // mark product as deactivated
        $sSql = "UPDATE jxstockcollecturls SET jxdeactivation = NOW() WHERE jxartnum = '{$sArtnum}' ";
        $stmt = $this->dbh->prepare($sSql);
        $stmt->execute();
        
        //deactivate product
        $sSql = "UPDATE oxarticles SET oxactive = 0, oxstock = 0 WHERE oxartnum = '{$sArtnum}' ";
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
