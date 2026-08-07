<?php

declare(strict_types=1);

namespace Bb\ConsentBanner\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * A single cookie set by a consent component. Components can hold any number of
 * these as inline (IRRE) child records.
 */
class Cookie extends AbstractEntity
{
    protected string $cookieName = '';
    protected string $cookieType = '';
    protected string $cookieDescription = '';
    protected string $cookiePurpose = '';
    protected string $cookieLifetime = '';

    public function getCookieName(): string
    {
        return $this->cookieName;
    }

    public function setCookieName(string $cookieName): void
    {
        $this->cookieName = $cookieName;
    }

    public function getCookieType(): string
    {
        return $this->cookieType;
    }

    public function setCookieType(string $cookieType): void
    {
        $this->cookieType = $cookieType;
    }

    public function getCookieDescription(): string
    {
        return $this->cookieDescription;
    }

    public function setCookieDescription(string $cookieDescription): void
    {
        $this->cookieDescription = $cookieDescription;
    }

    public function getCookiePurpose(): string
    {
        return $this->cookiePurpose;
    }

    public function setCookiePurpose(string $cookiePurpose): void
    {
        $this->cookiePurpose = $cookiePurpose;
    }

    public function getCookieLifetime(): string
    {
        return $this->cookieLifetime;
    }

    public function setCookieLifetime(string $cookieLifetime): void
    {
        $this->cookieLifetime = $cookieLifetime;
    }
}
