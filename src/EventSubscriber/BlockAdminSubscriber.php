<?php
/**
 * Created by PhpStorm.
 * User: Levi.kim
 * Date: 13/12/2017
 * Time: 11:21
 */
namespace Drupal\blockadmin\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Url;
use Drupal\Core\Site\Settings;

/**
 * Redirect admin and user/login pages to corresponding Node page.
 */
class blockadminSubscriber implements EventSubscriberInterface {

  /** @var int */
  private $redirectCode = 301;

  /**
   * Redirect pattern based url
   * @param GetResponseEvent $event
   */
  public function blockadminion(GetResponseEvent $event) {

    $request = \Drupal::request();
    $requestUrl = $request->server->get('REQUEST_URI', NULL);
    $route_name = \Drupal::service('current_route_match')->getRouteName();
    $route_name_forbidden = ["user.pass", "user.login","system.403"];

    if (Settings::get('blockadmin') === TRUE && in_array($route_name, $route_name_forbidden) && ( !(strpos($requestUrl, '/admin' )===FALSE) || !(strpos($requestUrl,'/user')===FALSE))) {
      $response = new RedirectResponse('/', $this->redirectCode);
      $response->headers->set('X-Status-Code', $this->redirectCode);
      $response->send();
      exit(0);
    }


  }

  /**
   * Listen to kernel.request events and call blockadminion.
   * {@inheritdoc}
   * @return array Event names to listen to (key) and methods to call (value)
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('blockadminion');
    return $events;
  }
}