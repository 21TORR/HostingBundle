<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment\Exception;

use Torr\Hosting\Exception\HostingException;

/**
 * Thrown by hooks to signal that they failed.
 */
final class HookFailedException extends \RuntimeException implements HostingException
{
}
