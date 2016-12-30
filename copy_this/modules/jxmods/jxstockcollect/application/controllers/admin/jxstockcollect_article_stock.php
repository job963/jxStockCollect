<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class jxstockcollect_article_stock extends jxstockcollect_article_stock_parent
{
    public function render()
    {
        $mReturn = parent::render();
        $this->_aViewData["jxstockcollect"] = $this->jxsc_LoadDelivererStockData(); 
                                            /*array(
                                                "url"      => "https://www.akah.de/rucksaecke/akah-einhand-rucksack-61311000",
                                                "pattern"  => "Akah",
                                                "delstock" => "available",
                                                "httpcode" => "200"
                                                );*/
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
        /*echo '<hr><pre>';
        print_r ($oArticle);
        echo '</pre>';
        echo $oArticle->oxarticles__oxartnum->rawValue;*/
        $aParams['jxartnum'] = $oArticle->oxarticles__oxartnum->rawValue;
        
        $this->jxsc_SaveDelivererStockData($aParams);
        
    }
    
    
    
    public function jxsc_SaveDelivererStockData($aParams) {
        /*echo '<pre>';
        print_r ($aParams);
        echo '</pre>';*/
        if ($aParams['jxurl'] != "") {
            $oDb = oxDb::getDb();
            $sSql = "INSERT INTO jxstockcollecturls "
                        . "(jxactive, jxurl, jxpatterntype, jxartnum, jxstock) "
                        . "VALUES (1, ".$oDb->quote($aParams['jxurl']).", ".$oDb->quote($aParams['jxpatterntype']).", ".$oDb->quote($aParams['jxartnum']).", ".$oDb->quote($aParams['jxstock']).") "
                    . "ON DUPLICATE KEY UPDATE "
                        . "jxactive = ".$oDb->quote($aParams['jxactive']).", jxurl = ".$oDb->quote($aParams['jxurl']).", jxpatterntype = ".$oDb->quote(strtolower($aParams['jxpatterntype'])).", jxartnum = ".$oDb->quote($aParams['jxartnum']).", jxstock = ".$oDb->quote($aParams['jxstock'])." ";
            //echo $sSql;
            $oDb->execute($sSql);
        }
    }
    
    
    
    public function jxsc_LoadDelivererStockData() {
        
        $soxId = $this->getEditObjectId();
        
        
        $oDb = oxDb::getDb();
        $sDbName = oxRegistry::getConfig()->getConfigParam('dbName');
        try {
            //$sSql = "SELECT jxactive, jxurl, jxpatterntype, jxdelstock, jxhttpcode FROM jxstockcollecturls WHERE jxartnum = (SELECT oxartnum FROM oxarticles WHERE oxid = '{$soxId}')";
            $sSql = "SELECT jxactive, jxurl, jxpatterntype, jxstock, jxdelstock, jxhttpcode, oxid, oxstock "
                    . "FROM jxstockcollecturls, oxarticles "
                    . "WHERE jxartnum = oxartnum AND oxid = '{$soxId}' ";

            $oDb->setFetchMode(oxDb::FETCH_MODE_ASSOC);
            $aRet = $oDb->getRow($sSql);
            /*echo '<pre>';
            var_dump($blRet);
            echo '</pre>';*/
        }
        catch(Exception $oEx) {
            $aRet = false;
        }
        return $aRet;
    }
    
}