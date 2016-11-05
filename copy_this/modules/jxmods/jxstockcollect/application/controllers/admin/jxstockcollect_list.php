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
        
        $sWhere = "";
        if ( is_array( $aWhere = $this->getConfig()->getRequestParameter( 'jxwhere' ) ) ) {
            $sWhere = $this->_defineWhere( $aWhere );
        }
        
        $sPictureUrl = $myConfig->getPictureUrl(FALSE) . 'master/product';
        /*$sIconCol1 = "IF(a.oxicon!='',"
                        . "CONCAT('{$sPictureUrl}/icon/',a.oxicon),"
                        . "IF(a.oxpic1!='',CONCAT('{$sPictureUrl}/1/',a.oxpic1),'')) "
                        . "AS picname";
        $sIconCol2 = "(SELECT "
                        . "IF(b.oxicon!='',"
                            . "CONCAT('{$sPictureUrl}/icon/',b.oxicon),"
                            . "IF(b.oxpic1!='',CONCAT('{$sPictureUrl}/1/',b.oxpic1),'')) "
                        . "FROM oxarticles b "
                        . "WHERE a.oxparentid = b.oxid) "
                        . "AS picname ";*/
        $sPicName = "IF(a.oxparentid='',"
                    . "IF(a.oxicon!='',"
                        . "CONCAT('{$sPictureUrl}/icon/',a.oxicon),"
                        . "IF(a.oxpic1!='',CONCAT('{$sPictureUrl}/1/',a.oxpic1),''))"
                    . ","
                    . "(SELECT "
                        . "IF(b.oxicon!='',"
                            . "CONCAT('{$sPictureUrl}/icon/',b.oxicon),"
                            . "IF(b.oxpic1!='',CONCAT('{$sPictureUrl}/1/',b.oxpic1),'')) "
                        . "FROM oxarticles b "
                        . "WHERE a.oxparentid = b.oxid)"
                    . ") AS picname ";

        $sSql = "SELECT DISTINCT u.jxpatterntype, u.jxactive, u.jxartnum, u.jxurl, u.jxdeactivation, u.jxhttpcode, u.jxdelstock, jxartupdated, u.jxtimestamp, "
                    . "a.oxid, a.oxactive, a.oxstock, {$sPicName}, "
                    . "IF(a.oxparentid='', "
                        . "a.oxtitle, "
                        . "CONCAT((SELECT a1.oxtitle FROM oxarticles a1 WHERE a.oxparentid = a1.oxid), ', ', a.oxvarselect)) AS oxfulltitle "
                . "FROM jxstockcollecturls u "
                . "LEFT JOIN oxarticles a "
                    . "ON (u.jxartnum = a.oxartnum) "
                . "WHERE u.jxartnum != '' "
                    . $sWhere
                . "ORDER BY u.jxpatterntype, oxfulltitle";

        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        
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
        $this->_aViewData["aWhere"] = $aWhere;
        
        $this->_aViewData["sShopUrl"] = $myConfig->getShopURL();

        $oModule = oxNew('oxModule');
        $oModule->load('jxstockcollect');
        $this->_aViewData["sModuleId"] = $oModule->getId();
        $this->_aViewData["sModuleVersion"] = $oModule->getInfo('version');

        return $this->_sThisTemplate;
    }
    
    
    private function _defineWhere( $aWhere )
    {
        if ($aWhere['jxactive'] != '')
            $sWhere .= "AND u.jxactive LIKE '%".$aWhere['jxactive']."%' ";
        if ($aWhere['oxactive'] != '')
            $sWhere .= "AND a.oxactive LIKE '%".$aWhere['oxactive']."%' ";
        if ($aWhere['jxartnum'] != '')
            $sWhere .= "AND u.jxartnum LIKE '%".$aWhere['jxartnum']."%' ";
        if ($aWhere['oxfulltitle'] != '')
            $sWhere .= "AND IF(a.oxparentid = '', a.oxtitle, (SELECT b.oxtitle FROM oxarticles b where b.oxid = a.oxparentid)) LIKE '%".$aWhere['oxfulltitle']."%' ";
        if ($aWhere['jxpatterntype'] != '')
            $sWhere .= "AND u.jxpatterntype LIKE '%".$aWhere['jxpatterntype']."%' ";
        if ($aWhere['oxstock'] != '')
            $sWhere .= "AND a.oxstock = ".$aWhere['oxstock']." ";
        if ($aWhere['jxhttp'] != '')
            $sWhere .= "AND u.jxhttpcode LIKE '".$aWhere['jxhttp']."%' ";
        
        return $sWhere;
    }
    
}
