<?php

namespace Drupal\common\FileFetcher;

use Contracts\FactoryInterface;
use Drupal\common\Storage\JobStoreFactory;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use FileFetcher\FileFetcher;

/**
 * File fetcher Factory.
 */
class FileFetcherFactory implements FactoryInterface {

  /**
   * Job store factory service.
   *
   * @var \Drupal\common\Storage\JobStoreFactory
   */
  private JobStoreFactory $jobStoreFactory;

  /**
   * The common.settings config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private ImmutableConfig $dkanConfig;

  /**
   * Default file fetcher config.
   *
   * @var array
   */
  private array $configDefault = [
    'keep_original_filename' => TRUE,
  ];

  /**
   * Constructor.
   */
  public function __construct(JobStoreFactory $jobStoreFactory, ConfigFactoryInterface $configFactory) {
    $this->jobStoreFactory = $jobStoreFactory;
    $this->dkanConfig = $configFactory->get('common.settings');
  }

  /**
   * {@inheritDoc}
   */
  public function getInstance(string $identifier, array $config = []) {
    $config = array_merge($this->configDefault, $config);
    $file_fetcher = DkanFileFetcher::get(
      $identifier,
      $this->jobStoreFactory->getInstance(FileFetcher::class),
      $config
    );
    // Inject our special configuration into the file fetcher, so it can use
    // local files rather than re-downloading them.
    $file_fetcher->setAlwaysUseExistingLocalPerspective(
      (bool) $this->dkanConfig->get('always_use_existing_local_perspective')
    );
    return $file_fetcher;
  }

}
