<?php
/**
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
 * @copyright (C) 2016 Joachim Barthel
 * @author    Joachim Barthel <jobarthel@gmail.com>
 *
 */

class jxstockcollect_list extends oxAdminDetails {

    protected $_sThisTemplate = "jxstockcollect_list.tpl";

    /**
     * Displays the collecting urls 
     */
    public function render() 
    {
        parent::render();

        $myConfig = oxRegistry::getConfig();
        
        /*if ($myConfig->getBaseShopId() == 'oxbaseshop') {
            // CE or PE shop
            $sWhereShopId = "";
        } else {
            // EE shop
            $sWhereShopId = "AND l.oxshopid = {$myConfig->getBaseShopId()} ";
        }*/
        
        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );

        $sSql = "SELECT DISTINCT u.jxactive, u.jxartnum, u.jxurl, u.jxdeactivation, u.jxhttpcode, u.jxtimestamp, a.oxstock, "
                . "IF(a.oxparentid='', "
                    . "a.oxtitle, "
                    . "CONCAT((SELECT a1.oxtitle FROM oxarticles a1 WHERE a.oxparentid = a1.oxid), ', ', a.oxvarselect)) AS oxfulltitle "
                . "FROM jxstockcollecturls u, oxarticles a "
                . "WHERE u.jxartnum = a.oxartnum "
                . "ORDER BY u.jxartnum ";
        
        try {
            $rs = $oDb->Select($sSql);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
        
        $aArticles = array();
        if ($rs) {
            while (!$rs->EOF) {
                array_push($aArticles, $rs->fields);
                $rs->MoveNext();
            }
        }
        
        $this->_aViewData["aArticles"] = $aArticles;
        
        $this->_aViewData["sShopUrl"] = $myConfig->getShopURL();

        $oModule = oxNew('oxModule');
        $oModule->load('jxstockcollect');
        $this->_aViewData["sModuleId"] = $oModule->getId();
        $this->_aViewData["sModuleVersion"] = $oModule->getInfo('version');

        return $this->_sThisTemplate;
    }
    
/*    
    public function saveNewSeoUrls()
    {
        $myConfig = oxRegistry::getConfig();
        if ($myConfig->getBaseShopId() == 'oxbaseshop') {
            // CE or PE shop
            $sShopId = "'oxbaseshop' ";
        } else {
            // EE shop
            $sShopId = $myConfig->getBaseShopId();
        }
        $iLang = $this->_iEditLang;

        $oDb = oxDb::getDb();
        $a404Urls = $this->getConfig()->getRequestParameter( 'jx404_404urls' ); 
        $aSeoUrls = $this->getConfig()->getRequestParameter( 'jx404_seourls' ); 

        foreach ($aSeoUrls as $key => $sSeoUrl) {
            if ($sSeoUrl != '') {
                $sSql = "SELECT oxobjectid "
                        . "FROM oxseo "
                        . "WHERE oxseourl = '{$sSeoUrl}' "
                            . "AND oxlang = {$iLang} "
                            . "AND oxshopid = {$sShopId} "
                        . "LIMIT 1";
                $sObjectId = $oDb->getOne($sSql);

                if ($sObjectId != '') {
                    if ($oDb->getOne("SELECT oxobjectid FROM oxseohistory WHERE oxident = MD5(LOWER('{$a404Urls[$key]}')) AND oxshopid = {$sShopId} AND oxlang = {$iLang} ") == '') {
                        $sSql = "INSERT INTO oxseohistory "
                                . "(oxobjectid, oxident, oxshopid, oxlang, oxhits, oxinsert) "
                                . "VALUES "
                                . "('{$sObjectId}', MD5(LOWER('{$a404Urls[$key]}')), {$sShopId}, {$iLang}, 0, NOW())";
                        $oDb->execute($sSql);
                    }
                    else {
                        $sSql = "UPDATE oxseohistory "
                                . "SET oxobjectid = " . $oDb->quote($sObjectId) . " "
                                . "WHERE oxident = MD5(LOWER('{$a404Urls[$key]}')) "
                                    . "AND oxshopid = {$sShopId} "
                                    . "AND oxlang = {$iLang} ";
                        $oDb->execute($sSql);
                    }
                }
                else {
                    echo "URL '{$sSeoUrl}' not found.<br>";
                }
            }
            
        }
        return;
    }

	
    public function RemoveUrl() 
    {
        $sOxIdent = $this->getConfig()->getRequestParameter( 'oxident' ); 
        
        $oDb = oxDb::getDb();
        $sSql = "DELETE FROM oxseohistory WHERE oxident = '{$sOxIdent}' ";

        $oDb->execute($sSql);
        
        return;
    }
*/    
}
