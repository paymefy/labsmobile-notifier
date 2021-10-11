<?php

/*
 * (c) Paymefy <hello@paymefy.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Paymefy\Component\Notifier\Bridge\LabsMobile\Tests;

use Paymefy\Component\Notifier\Bridge\LabsMobile\LabsMobileTransportFactory;
use Symfony\Component\Notifier\Test\TransportFactoryTestCase;
use Symfony\Component\Notifier\Transport\TransportFactoryInterface;

final class LabsMobileTransportFactoryTest extends TransportFactoryTestCase
{
    /**
     * @return LabsMobileTransportFactory
     */
    public function createFactory(): TransportFactoryInterface
    {
        return new LabsMobileTransportFactory();
    }

    public function createProvider(): iterable
    {
        yield [
            'labsmobile://host.test?from=0611223344',
            'labsmobile://username:apiToken@host.test?from=0611223344',
        ];
    }

    public function supportsProvider(): iterable
    {
        yield [true, 'labsmobile://username:apiToken@default?from=0611223344'];
        yield [false, 'somethingElse://username:apiToken@default?from=0611223344'];
    }

    public function missingRequiredOptionProvider(): iterable
    {
        yield 'missing option: from' => ['labsmobile://username:apiToken@default'];
    }

    public function unsupportedSchemeProvider(): iterable
    {
        yield ['somethingElse://username:apiToken@default?from=0611223344'];
        yield ['somethingElse://username:apiToken@default']; // missing "from" option
    }
}
