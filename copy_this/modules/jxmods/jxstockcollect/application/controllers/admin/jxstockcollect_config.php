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

class jxstockcollect_config extends oxAdminDetails {

    protected $_sThisTemplate = "jxstockcollect_config.tpl";

    /**
     * Displays the collected urls 
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
        

        $sSql = "SELECT * FROM jxstockcollectpatterns "
                . "ORDER BY jxpatterntype ";

        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        
        try {
            $rs = $oDb->Select($sSql);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
        
        $aPatterns = array();
        if ($rs) {
            while (!$rs->EOF) {
                array_push($aPatterns, $rs->fields);
                $rs->MoveNext();
            }
        }
        
        $this->_aViewData["aPatterns"] = $aPatterns;

        $oModule = oxNew('oxModule');
        $oModule->load('jxstockcollect');
        $this->_aViewData["sModuleId"] = $oModule->getId();
        $this->_aViewData["sModuleVersion"] = $oModule->getInfo('version');

        return $this->_sThisTemplate;
    }
    
    
    public function createStockPattern() 
    {
        $aParams = oxRegistry::getConfig()->getRequestParameter("newval");
        
        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );

        $aFields = array();
        $aValues = array();
        foreach ($aParams as $sField => $sValue) {
            $aFields[] = $sField;
            $aValues[] = $oDb->getDb()->quote($sValue);
        }
        
        $sSql = "INSERT INTO jxstockcollectpatterns (" . implode(', ', $aFields) . ") VALUES (" . implode(', ', $aValues) . ") ";
        $oDb->execute($sSql);
    }
    
    
    public function saveStockPatterns() 
    {
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");

        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        
        foreach ($aParams as $sPattern => $aRow) {
            $aPairs = array();
            foreach ($aRow as $sField => $sValue) {
                if ($sField == "jxpatterntype") {
                    $sValue = strtolower($sValue);
                }
                $aPairs[] = $sField . '=' . $oDb->getDb()->quote($sValue);
            }
            $sSql = "UPDATE jxstockcollectpatterns SET " . implode(', ', $aPairs) . " WHERE jxpatterntype = " . $oDb->getDb()->quote($sPattern) . " ";
            $oDb->execute($sSql);
        }
        
    }
    
    
    public function deleteStockPattern() 
    {
        $sPattern = oxRegistry::getConfig()->getRequestParameter("oxident");

        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        
        $sSql = "DELETE FROM jxstockcollectpatterns WHERE jxpatterntype = " . $oDb->getDb()->quote($sPattern) . " ";
        $oDb->execute($sSql);
    }
    
}
