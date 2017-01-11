[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
function deleteThis( sID)
{
    blCheck = confirm("[{ oxmultilang ident="GENERAL_YOUWANTTODELETE" }]");
    if( blCheck == true)
    {
        var oSearch = document.getElementById("jxstockcollect_delete");
        oSearch.fnc.value='deleteStockPattern';
        oSearch.oxident.value=sID;
        oSearch.submit();
    }
}

function editThis( sID )
{
    [{assign var="shMen" value=1}]

    [{foreach from=$menustructure item=menuholder }]
      [{if $shMen && $menuholder->nodeType == XML_ELEMENT_NODE && $menuholder->childNodes->length }]

        [{assign var="shMen" value=0}]
        [{assign var="mn" value=1}]

        [{foreach from=$menuholder->childNodes item=menuitem }]
          [{if $menuitem->nodeType == XML_ELEMENT_NODE && $menuitem->childNodes->length }]
            [{ if $menuitem->getAttribute('id') == 'mxorders' }]

              [{foreach from=$menuitem->childNodes item=submenuitem }]
                [{if $submenuitem->nodeType == XML_ELEMENT_NODE && $submenuitem->getAttribute('cl') == 'admin_order' }]

                    if ( top && top.navigation && top.navigation.adminnav ) {
                        var _sbli = top.navigation.adminnav.document.getElementById( 'nav-1-[{$mn}]-1' );
                        var _sba = _sbli.getElementsByTagName( 'a' );
                        top.navigation.adminnav._navAct( _sba[0] );
                    }

                [{/if}]
              [{/foreach}]

            [{ /if }]
            [{assign var="mn" value=$mn+1}]

          [{/if}]
        [{/foreach}]
      [{/if}]
    [{/foreach}]

    var oTransfer = document.getElementById("transfer");
    oTransfer.oxid.value=sID;
    oTransfer.cl.value='article';
    oTransfer.submit();
}

</script>


[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]


<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="jxstockcollect_list">
</form>

[{assign var="oConfig" value=$oViewConf->getConfig() }]
               
<h2>[{ oxmultilang ident="JXSTOCKCOLLECT_CONFIG_HEADER" }]</h2>                       
    <form name="jxstockcollect_delete" id="jxstockcollect_delete" action="[{ $oViewConf->getSelfLink() }]" method="post">
        [{ $oViewConf->getHiddenSid() }]
        [{*<input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">*}]
        <input type="hidden" name="cl" value="jxstockcollect_config">
        <input type="hidden" name="fnc" value="deleteStockPattern">
        <input type="hidden" name="oxident" value="">
    </form>
            
    <table cellspacing="0" cellpadding="0" border="0" width="99%">
        <tr>
            <td valign="top" class="listheader first" style="background-color: darkgray; border-right: 2px solid white;">
                [{ oxmultilang ident="JXSTOCKCOLLECT_CONFIG_PATTERNTYPE" }]
            </td>
            <td valign="top" class="listheader" style="background-color: darkgray; border-right: 2px solid white;">
                [{ oxmultilang ident="JXSTOCKCOLLECT_CONFIG_PATTERNREGEX" }]
            </td>
            <td valign="top" class="listheader" style="background-color: darkgray; border-right: 2px solid white;">
                [{ oxmultilang ident="JXSTOCKCOLLECT_CONFIG_AVAILABLETEXT" }]
            </td>
            <td valign="top" class="listheader" style="background-color: darkgray; border-right: 2px solid white;">
                [{ oxmultilang ident="JXSTOCKCOLLECT_CONFIG_LOWSTOCKTEXT" }]
            </td>
            <td valign="top" class="listheader" style="background-color: darkgray; border-right: 2px solid white;">
                [{ oxmultilang ident="JXSTOCKCOLLECT_CONFIG_OUTOFSTOCKTEXT" }]
            </td>
            <td valign="top" class="listheader" style="background-color: darkgray;">
                &nbsp;
            </td>
        </tr>
        <form name="jxstockcollect_edit" id="jxstockcollect_edit" action="[{ $oViewConf->getSelfLink() }]" method="post">
            [{ $oViewConf->getHiddenSid() }]
            [{*<input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">*}]
            <input type="hidden" name="cl" value="jxstockcollect_config">
            <input type="hidden" name="fnc" value="saveStockPatterns">
            <input type="hidden" name="oxident" value="">
            [{foreach item=aPattern from=$aPatterns}]
                [{ cycle values="listitem,listitem2" assign="listclass" }]
                [{*assign var="cntArticles" value=$cntArticles+1 *}]
                <tr>
                    <td class="[{ $listclass }]">
                        <input type="text" name="editval[[{$aPattern.jxpatterntype}]][jxpatterntype]" value="[{$aPattern.jxpatterntype|ucfirst}]" size="10">
                    </td>
                    <td class="[{ $listclass }]">
                        <input type="text" name="editval[[{$aPattern.jxpatterntype}]][jxpattern]" value="[{$aPattern.jxpattern|escape}]" size="60">
                    </td>
                    <td class="[{ $listclass }]">
                        <input type="text" name="editval[[{$aPattern.jxpatterntype}]][jxavailabletext]" value="[{$aPattern.jxavailabletext|escape}]" size="25">
                    </td>
                    <td class="[{ $listclass }]">
                        <input type="text" name="editval[[{$aPattern.jxpatterntype}]][jxlowstocktext]" value="[{$aPattern.jxlowstocktext|escape}]" size="25">
                    </td>
                    <td class="[{ $listclass }]">
                        <input type="text" name="editval[[{$aPattern.jxpatterntype}]][jxoutofstocktext]" value="[{$aPattern.jxoutofstocktext|escape}]" size="25">
                    </td>
                    <td class="[{ $listclass}]">
                        <a href="Javascript:deleteThis('[{ $aPattern.jxpatterntype }]');" class="delete"[{include file="help.tpl" helpid=item_delete}]></a>
                    </td>
            [{/foreach}]
            <tr>
                <td colspan="4"></td>
                <td colspan="2" align="right">
                    <input type="submit" value="[{ oxmultilang ident="JXSTOCKCOLLECT_CONFIG_UPDATE" }]">
                </td>
            </tr>
        </form>
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>
        <form name="jxstockcollect_addnew" id="jxstockcollect" action="[{ $oViewConf->getSelfLink() }]" method="post">
            [{ $oViewConf->getHiddenSid() }]
            [{*<input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">*}]
            <input type="hidden" name="cl" value="jxstockcollect_config">
            <input type="hidden" name="fnc" value="createStockPattern">
            <input type="hidden" name="oxident" value="">
            <tr>
                <td>
                    <input type="text" name="newval[jxpatterntype]" value="" size="10">
                </td>
                <td>
                    <input type="text" name="newval[jxpattern]" value="" size="60">
                </td>
                <td>
                    <input type="text" name="newval[jxavailabletext]" value="" size="25">
                </td>
                <td>
                    <input type="text" name="newval[jxlowstocktext]" value="" size="25">
                </td>
                <td>
                    <input type="text" name="newval[jxoutofstocktext]" value="" size="25">
                </td>
                <td align="right">
                    <input type="submit" value="[{ oxmultilang ident="JXSTOCKCOLLECT_CONFIG_ADDNEW" }]">
                </td>
            </tr>
        </form>
    </table>
</div>

[{*include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"*}]

<script type="text/javascript">
  if(top)
  {
    top.sMenuItem    = "[{ oxmultilang ident="mxmanageprod" }]";
    top.sMenuSubItem = "[{ oxmultilang ident="jxstockcollect_list" }]";
    top.sWorkArea    = "[{$_act}]";
    top.setTitle();
  }
</script>

</body>
</html>
