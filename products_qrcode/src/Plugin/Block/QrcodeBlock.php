<?php
/**
 * @file
 * Contains \Drupal\products_qrcode\Plugin\Block
 * @Block (
 *	id = "prod_qrcode_block",
 *	admin_label = "QRcode Block",
 * )
 */
namespace Drupal\products_qrcode\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\node\NodeInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;

class QrcodeBlock extends BlockBase implements ContainerFactoryPluginInterface {
  
  /**
   * RouteMatch used to get parameter Node.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a Drupal\product_barcode\Plugin\block\BarcodeBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }



  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
  	$purchase_link = '';
  	if (($node = $this->routeMatch->getParameter('node')) && $node->bundle() === 'products') {
  	  $purchase_link = $node->field_purchase_link->uri;
    

  	 // instantiate the barcode class
  	  $barcode = new \Com\Tecnick\Barcode\Barcode();
  	  // generate a barcode
  	  $bobj = $barcode->getBarcodeObj(
  	    'QRCODE,H', $purchase_link, -6, -6, 'black', array(-2, -2, -2, -2))->setBackgroundColor('white'); 

  	  // output the barcode as HTML div
  	  $qrcode = $bobj->getHtmlDiv();

      $build = [
        '#type' => 'inline_template',
        '#template' => $qrcode,
        '#cache' =>[
            'contexts' => ['url'],
          ],
       ];
    }
    return $build;
  }
}