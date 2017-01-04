[{$smarty.block.parent}]

<link href="[{$oViewConf->getModuleUrl('jxStockCollect','out/admin/src/jxstockcollect.css')}]" type="text/css" rel="stylesheet">
[{*debug*}]
				  
<tr>
    <td class="edittext" colspan="2"><br>
        <fieldset title="[{ oxmultilang ident="JXSTOCKCOLLECT_DELIVERER_STOCK" }]" style="padding-left: 5px;">
            <legend>[{ oxmultilang ident="JXSTOCKCOLLECT_DELIVERER_STOCK" }]</legend>
            <table>
                <tr>
                    <td class="edittext" style="width: 160px;">
                        [{ oxmultilang ident="ARTICLE_MAIN_ACTIVE" }]
                    </td>
                    <td class="edittext">
                        <input type="checkbox" class="editinput" name="editval[jxactive]" value='[{if $jxstockcollect.jxactive }][{ $jxstockcollect.jxactive }][{else}]1[{/if}]' [{if $jxstockcollect.jxactive }]checked[{/if}] [{ $readonly }]>
                    </td>
                </tr>
                <tr>
                    <td class="edittext" style="width: 160px;">
                        [{ oxmultilang ident="JXSTOCKCOLLECT_COLLECT_URL" }]
                    </td>
                    <td class="edittext">
                        <input type="text" size="60" name="editval[jxurl]" value="[{$jxstockcollect.jxurl}]">
                        [{if $jxstockcollect.jxurl != ""}]
                            <a href="[{$jxstockcollect.jxurl}]" target="_blank"><span style="font-weight:bold; font-size:1.2em; border:1px solid lightgrey; background-color:#e0e0e0; border-radius:2px; padding: 0 3px 0 3px;">&nearr;</span></a>
                        [{/if}]
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                        [{ oxmultilang ident="JXSTOCKCOLLECT_COLLECT_PATTERN" }]
                    </td>
                    <td class="edittext">
                        <input type="text" size="20" name="editval[jxpatterntype]" value="[{$jxstockcollect.jxpatterntype|ucfirst}]">
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                        [{ oxmultilang ident="JXSTOCKCOLLECT_DEFAULT_STOCK" }]
                    </td>
                    <td class="edittext">
                        <input type="text" size="20" name="editval[jxstock]" value="[{$jxstockcollect.jxstock}]">
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                        [{ oxmultilang ident="JXSTOCKCOLLECT_COLLECTED_STOCK" }]
                    </td>
                    <td class="edittext">
                        [{if $jxstockcollect.jxhttpcode == "" and $jxstockcollect.jxurl != ""}]
                            <span style="color:gray; font-style:italic;">[{ oxmultilang ident="JXSTOCKCOLLECT_UNCHECKED" }]</span>
                        [{elseif $jxstockcollect.jxurl != ""}]
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
                            (<span class="httpcode[{$jxstockcollect.jxhttpcode}]">[{$jxstockcollect.jxhttpcode}]</span>),
                            [{$jxstockcollect.jxtimestamp}]
                        [{/if}]
                    </td>
                </tr>
            </table>
        </fieldset>
        </td>
</tr>
