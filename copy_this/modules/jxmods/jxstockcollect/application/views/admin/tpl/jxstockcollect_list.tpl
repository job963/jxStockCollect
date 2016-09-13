[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<link href="[{$oViewConf->getModuleUrl('jxStockCollect','out/admin/src/jxstockcollect.css')}]" type="text/css" rel="stylesheet">

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]


[{*<script type="text/javascript">
<!--
function RemoveUrl( sOxIdent )
{
    var oForm = document.getElementById("jx404");
    oForm.fnc.value = "removeUrl";
    oForm.oxident.value = sOxIdent;
    oForm.submit();
}
//-->
</script>*}]


[{*<style>
    #liste tr:hover td{
        background-color: #e0e0e0;
    }

    #liste td.activetime {
        background-image: url(bg/ico_activetime.png);
        min-width: 17px;
        background-position: center center;
        background-repeat: no-repeat;
    }
    .listitem, .listitem2 {
        padding-left: 4px;
        padding-right: 16px;
        white-space: nowrap;
    }
</style>*}]


<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="jxstockcollect_list">
</form>

[{assign var="oConfig" value=$oViewConf->getConfig() }]
[{*assign var="iUrlLength" value=$oConfig->getConfigParam("sJx404CatcherUrlLength") *}]
               
<form name="jxstockcollect" id="jxstockcollect" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
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
                    <td class="listheader first" height="15" width="30" align="center">
                        [{ oxmultilang ident="GENERAL_ACTIVTITLE" }]
                    </td>
                    <td class="listheader">[{ oxmultilang ident="GENERAL_ARTNUM" }]</td>
                    <td class="listheader">[{ oxmultilang ident="GENERAL_TITLE" }]/[{ oxmultilang ident="tbclarticle_variant" }]</td>
                    <td class="listheader">[{ oxmultilang ident="GENERAL_VENDOR" }]</td>
                    <td class="listheader">[{ oxmultilang ident="GENERAL_ARTICLE_OXSTOCK" }]</td>
                    <td class="listheader">[{ oxmultilang ident="JXSTOCKCOLLECT_HTTPCODE" }]</td>
                    <td class="listheader">[{ oxmultilang ident="JXSTOCKCOLLECT_LASTUPDATE" }]</td>
                    <td class="listheader">[{ oxmultilang ident="JXSTOCKCOLLECT_DEACTIVATION" }]</td>
                    [{*<td class="listheader"></td>*}]
                </tr>
                [{foreach item=aArticle from=$aArticles}]
                    [{ cycle values="listitem,listitem2" assign="listclass" }]
                    <tr>
                        <td valign="top" class="[{ $listclass}][{if $aArticle.jxactive == 1}] active[{/if}]" height="15">
                            <div class="listitemfloating">&nbsp</a></div>
                        </td>
                        <td class="[{ $listclass }]">[{$aArticle.jxartnum}]</td>
                        <td class="[{ $listclass }] titlecol">
                            <b>[{$aArticle.oxfulltitle}]</b><br />
                            <a href="[{$aArticle.jxurl}]" title="[{$aArticle.jxurl}]" target="_blank">[{$aArticle.jxurl}]</a>[{*<div style="height:8px;"></div>*}]
                        </td>
                        <td class="[{ $listclass }]">[{$aArticle.jxpatterntype|ucfirst}]</td>
                        <td class="[{ $listclass }]"><span class="[{if $aArticle.oxstock == 0 }]nostock[{/if}]">[{$aArticle.oxstock}]</span></td>
                        <td class="[{ $listclass }]"><span class="httpcode[{$aArticle.jxhttpcode}]">[{$aArticle.jxhttpcode}]</span></td>
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

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]

