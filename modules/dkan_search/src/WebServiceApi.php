<?php

namespace Drupal\dkan_search;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\dkan_common\JsonResponseTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Controller.
 */
class WebServiceApi implements ContainerInjectionInterface {
  use JsonResponseTrait;

  /**
   * Dkan search service.
   *
   * @var \Drupal\dkan_search\Service
   */
  private $service;

  /**
   * Request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  private $requestStack;

  /**
   * WebServiceApi constructor.
   *
   * @param \Drupal\dkan_search\Service $service
   *   Dkan search service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   Request stack.
   */
  public function __construct(
    Service $service,
    RequestStack $requestStack
  ) {
    $this->service = $service;
    $this->requestStack = $requestStack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dkan_search.service'),
      $container->get('request_stack')
    );
  }

  /**
   * Search.
   */
  public function search() {
    $params = $this->getParams();

    $responseBody = $this->service->search($params);

    return $this->getResponse($responseBody);
  }

  /**
   * Private.
   */
  private function getParams() {
    $defaults = [
      "page-size" => 10,
      "page" => 1,
    ];

    $request = $this->requestStack->getCurrentRequest();
    $params = $request->query->all();

    foreach ($defaults as $param => $default) {
      $params[$param] = isset($params[$param]) ? $params[$param] : $default;
    }

    if ($params["page-size"] > 100) {
      $params["page-size"] = 100;
    }

    return $params;
  }

}
