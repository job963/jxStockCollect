<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.1';
 
/**
 * Module information
 * 
 * @link      https://github.com/job963/jxStockCollect
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @copyright (C) Joachim Barthel 2016-2017
 * 
 **/

$aModule = array(
    'id'           => 'jxStockCollect',
    'title'        => 'jxStockCollect - Collects stock data from deliverer websites/shops',
    'description'  => array(
                        'de' => '<b>Ermitteln der Lagerstandsdaten von den Lieferanten Websites oder Webshops</b><ul>'
                                . '<li>Aufrufen der Lieferantenseiten'
                                . '<li>Analysieren der Lagerbestandsdaten'
                                . '<li>Aktualisieren der Shop-Artikel</ul>',
                        'en' => '<b>Collecting of stock data from deliverer websites or webshops</b><ul>'
                                . '<li>Retrieving the deliverer product pages'
                                . '<li>Analyzing the stock data'
                                . '<li>Updating the shop products</ul>',
                        ),
    'thumbnail'    => 'jxstockcollect.png',
    'version'      => '0.6.1',
    'author'       => 'Joachim Barthel',
    'url'          => 'https://github.com/job963/jxStockCollect',
    'email'        => 'jobarthel@gmail.com',
    'extend'       => array(
                            'article_stock'    =>      'jxmods/jxstockcollect/application/controllers/admin/jxstockcollect_article_stock' 
                        ),
    'files'        => array(
                            'jxstockcollect_events' => 'jxmods/jxstockcollect/application/events/jxstockcollect_events.php',
                            'jxstockcollect_list'   => 'jxmods/jxstockcollect/application/controllers/admin/jxstockcollect_list.php',
                            'jxstockcollect_article_stock'   => 'jxmods/jxstockcollect/application/controllers/admin/jxstockcollect_article_stock.php'
                        ),
    'templates'     => array(
                            'jxstockcollect_list.tpl' => 'jxmods/jxstockcollect/application/views/admin/tpl/jxstockcollect_list.tpl'
                        ),
    'blocks'        => array(
                            array( 
                                'template' => 'article_stock.tpl', 
                                'block'    => 'admin_article_stock_form',                     
                                'file'     => '/application/views/admin/blocks/admin_article_stock_form.tpl'
                              )
                        ),
    'events'       => array(
                            'onActivate'   => 'jxstockcollect_events::onActivate', 
                            'onDeactivate' => 'jxstockcollect_events::onDeactivate'
                        ),
   'settings'      => array(
                        )
);