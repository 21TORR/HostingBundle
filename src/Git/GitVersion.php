<?php declare(strict_types=1);

namespace Torr\Hosting\Git;

use Symfony\Contracts\Cache\CacheInterface;
use Torr\Hosting\Git\Data\InstalledGitCommit;

final class GitVersion
{
	private const CACHE_KEY = "21torr.hosting.git_version";
	private ?InstalledGitCommit $commit = null;
	private bool $fetched = false;
	private CacheInterface $cache;
	private GitVersionDumper $versionDumper;
	private bool $isDebug;
	private LocalGitVersionFetcher $localGitVersionFetcher;

	/**
	 */
	public function __construct(
		CacheInterface $cache,
		GitVersionDumper $versionDumper,
		LocalGitVersionFetcher $localGitVersionFetcher,
		bool $isDebug,
	)
	{
		$this->cache = $cache;
		$this->versionDumper = $versionDumper;
		$this->isDebug = $isDebug;
		$this->localGitVersionFetcher = $localGitVersionFetcher;
	}

	/**
	 * Returns the installed version
	 */
	public function getInstalledVersion () : ?InstalledGitCommit
	{
		if (!$this->fetched)
		{
			$this->fetched = true;
			$this->commit = !$this->isDebug
				? $this->cache->get(self::CACHE_KEY, function () { return $this->fetchVersion(); })
				: $this->fetchVersion();
		}

		return $this->commit;
	}


	/**
	 * Fetches the currently installed version
	 */
	private function fetchVersion (bool $forceRefresh = false) : ?InstalledGitCommit
	{
		// first check if there is a installed version file
		if (!$forceRefresh && null !== ($dumped = $this->versionDumper->load()))
		{
			return $dumped;
		}

		// then check live + write backup file
		$live = $this->localGitVersionFetcher->loadLocalVersion();
		$this->versionDumper->dump($live);
		return $live;
	}


	/**
	 * Refreshes the version
	 */
	public function refresh () : ?InstalledGitCommit
	{
		$commit = $this->fetchVersion(true);

		$this->cache->delete(self::CACHE_KEY);
		$this->commit = $commit;
		$this->fetched = true;

		return $commit;
	}
}
