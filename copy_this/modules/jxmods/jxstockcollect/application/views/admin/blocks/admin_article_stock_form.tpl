[{$smarty.block.parent}]

<link href="[{$oViewConf->getModuleUrl('jxStockCollect','out/admin/src/jxstockcollect.css')}]" type="text/css" rel="stylesheet">
[{*debug*}]
				  
<tr>
    <td class="edittext" colspan="2"><br>
        <fieldset title="Lieferantenlagerbestand" style="padding-left: 5px;">
            <legend>Lieferantenlagerbestand</legend>
            <table>
                <tr>
                    <td class="edittext" style="width: 160px;">
                        Aktiv
                    </td>
                    <td class="edittext">
                        [{*<input class="edittext" type="hidden" name="editval[oxcontents__pscmssnippets_disable]" value='0'>*}]
                        <input type="checkbox" class="editinput" name="editval[jxactive]" value='[{if $jxstockcollect.jxactive }][{ $jxstockcollect.jxactive }][{else}]1[{/if}]' [{if $jxstockcollect.jxactive }]checked[{/if}] [{ $readonly }]>
                        [{*<input class="edittext" type="checkbox" value='1' [{if $jxstockcollect.jxactive == 1}]checked[{/if}] [{ $readonly }]>*}]
                    </td>
                </tr>
                <tr>
                    <td class="edittext" style="width: 160px;">
                        Lieferanten-URL
                    </td>
                    <td class="edittext">
                        <input type="text" size="40" name="editval[jxurl]" value="[{$jxstockcollect.jxurl}]">
                        <a href="[{$jxstockcollect.jxurl}]" target="_blank"><span style="font-weight:bold; font-size:1.2em; border:1px solid lightgrey; background-color:#e0e0e0; border-radius:2px; padding: 0 3px 0 3px;">&nearr;</span></a>
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                        Erkennungsmuster
                    </td>
                    <td class="edittext">
                        <input type="text" size="20" name="editval[jxpatterntype]" value="[{$jxstockcollect.jxpatterntype|ucfirst}]">
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                        Standard Lagerbestand
                    </td>
                    <td class="edittext">
                        <input type="text" size="20" name="editval[jxstock]" value="[{$jxstockcollect.jxstock}]">
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                        Lagerbestand/Status
                    </td>
                    <td class="edittext">
                        [{if $jxstockcollect.jxurl != ""}]
                            <span class="[{if $jxstockcollect.oxstock == 0 and $jxstockcollect.jxdelstock != "available" }]nostock[{else}]instock[{/if}]">
                                [{if $jxstockcollect.oxstock > 0 }]
                                    [{ oxmultilang ident="JXSTOCKCOLLECT_DEL_available" }]
                                [{elseif $jxstockcollect.jxdelstock != ""}]
                                    [{ oxmultilang ident="JXSTOCKCOLLECT_DEL_"|cat:$jxstockcollect.jxdelstock }]
                                [{elseif $jxstockcollect.oxid == ""}]
                                    &nbsp;
                                [{else}]
                                    [{ oxmultilang ident="JXSTOCKCOLLECT_DEL_outofstock" }]
                                [{/if}]
                            </span>				  
                            (<span class="httpcode[{$jxstockcollect.jxhttpcode}]">[{$jxstockcollect.jxhttpcode}]</span>)
                        [{/if}]
                    </td>
                </tr>
            </table>
        </fieldset>
        </td>
</tr>
