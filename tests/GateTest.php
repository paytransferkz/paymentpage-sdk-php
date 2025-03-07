<?php

namespace paytransferkz\tests;

use paytransferkz\Callback;
use paytransferkz\Gate;
use paytransferkz\Payment;

class GateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private $testUrl = 'http://test-url.test/test';

    /**
     * @var Gate
     */
    private $gate;

    protected function setUp()
    {
        $this->gate = new Gate('secret', $this->testUrl);
    }

    public function testGetPurchasePaymentPageUrlEncrypted()
    {
        $this->gate = new Gate('test', '', 'qwerty');
        $payment = (new Payment(402))->setPaymentId('test payment id');
        $paymentUrl = $this->gate->getPurchasePaymentPageUrl($payment);

        self::assertRegExp('~^https://\w+.\w+.\w+/\d+/[\w\s/+=]+$~', $paymentUrl);
    }

    public function testGetPurchasePaymentPageUrl()
    {
        $payment = (new Payment(100))->setPaymentId('test payment id');
        $paymentUrl = $this->gate->getPurchasePaymentPageUrl($payment);

        self::assertNotEmpty($paymentUrl);
        self::assertStringStartsWith($this->testUrl, $paymentUrl);
    }

    public function testSetPaymentBaseUrl()
    {
        $someTestUrl = 'http://some-test-url.test/test/payment';

        self::assertEquals(Gate::class, get_class($this->gate->setPaymentBaseUrl($someTestUrl)));

        $paymentUrl = $this->gate->getPurchasePaymentPageUrl(new Payment(100));

        self::assertStringStartsWith($someTestUrl, $paymentUrl);
    }

    public function testHandleCallback()
    {
        $callback = $this->gate->handleCallback(require __DIR__ . '/data/callback.php');

        self::assertInstanceOf(Callback::class, $callback);
    }
}
