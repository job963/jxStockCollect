[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<link href="[{$oViewConf->getModuleUrl('jxStockCollect','out/admin/src/jxstockcollect.css')}]" type="text/css" rel="stylesheet">

<script type="text/javascript">
  if(top)
  {
    top.sMenuItem    = "[{ oxmultilang ident="mxmanageprod" }]";
    top.sMenuSubItem = "[{ oxmultilang ident="jxstockcollect_list" }]";
    top.sWorkArea    = "[{$_act}]";
    top.setTitle();
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
[{*assign var="iUrlLength" value=$oConfig->getConfigParam("sJx404CatcherUrlLength") *}]
               
<form name="jxstockcollect" id="jxstockcollect" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    [{*<input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">*}]
    <input type="hidden" name="cl" value="jxstockcollect_list">
    <input type="hidden" name="fnc" value="[{*saveNewSeoUrls*}]">
    <input type="hidden" name="oxident" value="">
                       
    <div id="liste">
            <div style="height: 12px;"></div>
            
            <table cellspacing="0" cellpadding="0" border="0" width="99%">
                [{*<colgroup>
                    <col width="3%">
                    <col width="8%">
                    <col width="34%">
                    <col width="8%">
                    <col width="8%">
                    <col width="5%">
                    <col width="10%">
                    <col width="10%">
                </colgroup>*}]
                <tr>
                    <td valign="top" class="listfilter first" align="right">
                        <div class="r1"><div class="b1">
                                <input class="listedit" type="text" size="2" maxlength="128" name="jxwhere[jxactive]" value="[{ $aWhere.jxactive }]">
                        </div></div>
                    </td>
                    <td valign="top" class="listfilter" align="right">
                        <div class="r1"><div class="b1">
                                <input class="listedit" type="text" size="2" maxlength="128" name="jxwhere[oxactive]" value="[{ $aWhere.oxactive }]">
                        </div></div>
                    </td>
                    <td class="listfilter"><div class="r1"><div class="b1">
                        <input class="listedit" type="text" size="10" maxlength="128" name="jxwhere[jxartnum]" value="[{ $aWhere.jxartnum }]">
                        </div></div>
                    </td>
                    <td class="listfilter"><div class="r1"><div class="b1">
                        <input class="listedit" type="text" size="30" maxlength="128" name="jxwhere[oxfulltitle]" value="[{ $aWhere.oxfulltitle }]">
                        </div></div>
                    </td>
                    <td class="listfilter"><div class="r1"><div class="b1">
                        <input class="listedit" type="text" size="10" maxlength="128" name="jxwhere[jxpatterntype]" value="[{ $aWhere.jxpatterntype }]">
                        </div></div>
                    </td>
                    <td class="listfilter"><div class="r1"><div class="b1">
                        <input class="listedit" type="text" size="6" maxlength="128" name="jxwhere[oxstock]" value="[{ $aWhere.oxstock }]">
                        </div></div>
                    </td>
                    <td class="listfilter"><div class="r1"><div class="b1">
                        <input class="listedit" type="text" size="6" maxlength="128" name="jxwhere[jxhttp]" value="[{ $aWhere.jxhttp }]">
                        </div></div>
                    </td>
                    <td valign="top" class="listfilter" align="right">
                        <div class="r1"><div class="b1">&nbsp;</div></div>
                    </td>
                    <td valign="top" class="listfilter" align="right">
                        <div class="r1"><div class="b1">&nbsp;</div></div>
                    </td>
                    <td class="listfilter"><div class="r1"><div class="b1"><div class="find">
                        <input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]">
                        </div></div></div>
                    </td>
                </tr>
                <tr>
                    <td class="listheader first" height="15" width="30" align="center">
                        [{ oxmultilang ident="JXSTOCKCOLLECT_URL" }]
                    </td>
                    <td class="listheader" height="15" width="30" align="center">
                        [{ oxmultilang ident="JXSTOCKCOLLECT_ART" }]
                    </td>
                    <td class="listheader">[{ oxmultilang ident="GENERAL_ARTNUM" }]</td>
                    <td class="listheader">[{ oxmultilang ident="GENERAL_TITLE" }]/[{ oxmultilang ident="tbclarticle_variant" }]</td>
                    <td class="listheader">[{ oxmultilang ident="GENERAL_VENDOR" }]</td>
                    <td class="listheader">[{ oxmultilang ident="GENERAL_ARTICLE_OXSTOCK" }]</td>
                    <td class="listheader">[{ oxmultilang ident="JXSTOCKCOLLECT_HTTPCODE" }]</td>
                    <td class="listheader">[{ oxmultilang ident="JXSTOCKCOLLECT_UPDATED" }]</td>
                    <td class="listheader">[{ oxmultilang ident="JXSTOCKCOLLECT_LASTCOLLECT" }]</td>
                    <td class="listheader">[{ oxmultilang ident="JXSTOCKCOLLECT_DEACTIVATION" }]</td>
                    [{*<td class="listheader"></td>*}]
                </tr>
                [{assign var="cntArticles" value=0}]
                [{assign var="cntCollects" value=0}]
                [{assign var="cntUpdates" value=0}]
                [{assign var="cntErrors" value=0}]
                [{foreach item=aArticle from=$aArticles}]
                    [{ cycle values="listitem,listitem2" assign="listclass" }]
                    [{assign var="cntArticles" value=$cntArticles+1 }]
                    <tr>
                        <td valign="top" class="[{ $listclass}][{if $aArticle.jxactive == 1}] active[{/if}]" height="15">
                            <div class="listitemfloating">&nbsp</a></div>
                        </td>
                        <td valign="top" class="[{ $listclass}][{if $aArticle.oxactive == 1}] active[{/if}]" height="15">
                            <div class="listitemfloating">&nbsp</a></div>
                        </td>
                        <td class="[{ $listclass }]">
                            <a class="thumbnail" href="Javascript:editThis('[{$aArticle.oxid}]');">[{$aArticle.jxartnum}]
                                <span><img src="[{$aArticle.picname}]" /></span></a>
                        </td>
                        <td class="[{ $listclass }] titlecol">
                            <b>[{$aArticle.oxfulltitle}]</b><br />
                            <a href="[{$aArticle.jxurl}]" title="[{$aArticle.jxurl}]" target="_blank">[{$aArticle.jxurl}]</a>[{*<div style="height:8px;"></div>*}]
                        </td>
                        <td class="[{ $listclass }]">[{$aArticle.jxpatterntype|ucfirst}]</td>
                        <td class="[{ $listclass }]">
                            <span class="[{if $aArticle.oxstock == 0 }]nostock[{else}]instock[{/if}]">[{if $aArticle.oxstock > 0 }][{$aArticle.oxstock}][{else}][{ oxmultilang ident="JXSTOCKCOLLECT_NOSTOCK" }][{/if}]</span>
                        </td>
                        <td class="[{ $listclass }]"><span class="httpcode[{$aArticle.jxhttpcode}]">[{$aArticle.jxhttpcode}]</span></td>
                        <td class="[{ $listclass }]">
                            <span class="[{if $aArticle.jxartupdated > 0 }]updateOk[{elseif $aArticle.jxartupdated == -1}]updateFromInv[{else}]updateErr[{/if}]">
                                [{if $aArticle.jxartupdated > 0 }]
                                    [{ oxmultilang ident="GENERAL_VENDOR" }]
                                    [{assign var="cntUpdates" value=$cntUpdates+1 }]
                                [{elseif $aArticle.jxartupdated == -1}]
                                    [{ oxmultilang ident="JXSTOCKCOLLECT_INVENTORY" }]
                                    [{assign var="cntUpdates" value=$cntUpdates+1 }]
                                [{else}]
                                    [{if $aArticle.jxhttpcode == ""}]
                                        [{ oxmultilang ident="JXSTOCKCOLLECT_UNCHECKED" }]
                                    [{else}]
                                        [{ oxmultilang ident="JXSTOCKCOLLECT_UPDATE_ERROR" }]
                                        [{assign var="cntErrors" value=$cntErrors+1 }]
                                    [{/if}]
                                [{/if}]
                            </span>
                        </td>
                        <td class="[{ $listclass }]"><span class="[{if $aArticle.jxtimestamp|date_format:"%D" != $smarty.now|date_format:"%D" }]outdated[{/if}]">[{$aArticle.jxtimestamp}]</span></td>
                        <td class="[{ $listclass }]">[{if $aArticle.jxdeactivation != "0000-00-00 00:00:00" }][{$aArticle.jxdeactivation}][{/if}]</td>
                        [{*<td class="[{ $listclass }]">
                            [{if !$readonly }]
                                <a href="Javascript:RemoveUrl('[{ $a404Url.oxident }]');" class="delete" id="del.[{$_cnt}]" title="" [{*include file="help.tpl" helpid=item_delete*}][{*></a>
                            [{/if}]
                        </td>*}]
                    </tr>
                [{/foreach}]
            </table>
    </div>
    [{*<input type="submit"
        value=" [{ oxmultilang ident="GENERAL_SAVE" }] " [{ $readonly }]>*}]
</form>

<table>
    <tr>
        <td><b>Gesamtanzahl</b></td>
        <td>[{$cntArticles}]</td>
    </tr>
    <tr>
        <td><b>Aktualisiert</b></td>
        <td>[{$cntUpdates}]</td>
    </tr>
    <tr>
        <td><b>Fehler</b></td>
        <td>[{$cntErrors}]</td>
    </tr>
</table>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]

