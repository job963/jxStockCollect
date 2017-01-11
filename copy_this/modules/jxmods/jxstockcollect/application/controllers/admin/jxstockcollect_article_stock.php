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
 * @copyright (C) 2016-2017 Joachim Barthel
 * @author    Joachim Barthel <jobarthel@gmail.com>
 *
 */

class jxstockcollect_article_stock extends jxstockcollect_article_stock_parent
{
    public function render()
    {
        $mReturn = parent::render();
        $this->_aViewData["jxstockcollect"] = $this->jxsc_LoadDelivererStockData(); 
        $this->_aViewData["jxstockpatterns"] = $this->jxsc_LoadDelivererPatterns(); 

        return $mReturn;
    }
    
    
    
    public function save()
    {
        parent::save();
        
        $soxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");

        // shopid
        $sShopID = oxRegistry::getSession()->getVariable("actshop");
        $aParams['oxarticles__oxshopid'] = $sShopID;

        $oArticle = oxNew("oxarticle");
        $oArticle->loadInLang($this->_iEditLang, $soxId);
        $aParams['jxartnum'] = $oArticle->oxarticles__oxartnum->rawValue;
        
        $this->jxsc_SaveDelivererStockData($aParams);
        
    }
    
    
    
    public function jxsc_LoadDelivererStockData() 
    {
        $soxId = $this->getEditObjectId();
        
        $oDb = oxDb::getDb();
        //$sDbName = oxRegistry::getConfig()->getConfigParam('dbName');
        try {
            $sSql = "SELECT jxactive, jxurl, jxpatterntype, jxstock, jxdelstock, jxhttpcode, jxoriginurl, jxredir, jxtimestamp, oxid, oxstock "
                    . "FROM jxstockcollecturls, oxarticles "
                    . "WHERE jxartnum = oxartnum AND oxid = '{$soxId}' ";

            $oDb->setFetchMode(oxDb::FETCH_MODE_ASSOC);
            $aRet = $oDb->getRow($sSql);
        }
        catch(Exception $oEx) {
            $aRet = false;
        }
        return $aRet;
    }
    
    
    
    public function jxsc_LoadDelivererPatterns() 
    {
        $oDb = oxDb::getDb();
        //$sDbName = oxRegistry::getConfig()->getConfigParam('dbName');
        try {
            $sSql = "SELECT jxpatterntype "
                    . "FROM jxstockcollectpatterns ";
            $rs = $oDb->Select($sSql);

            //$oDb->setFetchMode(oxDb::FETCH_MODE_ASSOC);
            $aPatterns = array();
            $aPatterns[] = "";
            if ($rs) {
                while (!$rs->EOF) {
                    array_push($aPatterns, $rs->fields[0]);
                    $rs->MoveNext();
                }
            }
        }
        catch(Exception $oEx) {
            $aPatterns = false;
        }
        return $aPatterns;
    }
    
    
    
    public function jxsc_SaveDelivererStockData($aParams) 
    {
        if ($aParams['jxurl'] != "") {
            $oDb = oxDb::getDb();
            $sSql = "INSERT INTO jxstockcollecturls "
                        . "(jxactive, jxurl, jxpatterntype, jxartnum, jxstock) "
                        . "VALUES (1, ".$oDb->quote($aParams['jxurl']).", ".$oDb->quote($aParams['jxpatterntype']).", ".$oDb->quote($aParams['jxartnum']).", ".$oDb->quote($aParams['jxstock']).") "
                    . "ON DUPLICATE KEY UPDATE "
                        . "jxactive = ".$oDb->quote($aParams['jxactive']).", jxurl = ".$oDb->quote($aParams['jxurl']).", jxpatterntype = ".$oDb->quote(strtolower($aParams['jxpatterntype'])).", jxartnum = ".$oDb->quote($aParams['jxartnum']).", jxstock = ".$oDb->quote($aParams['jxstock'])." ";
            $oDb->execute($sSql);
        }
    }
    
}