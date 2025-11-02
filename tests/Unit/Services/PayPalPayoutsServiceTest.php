<?php

// TODO: Complete PayPal service tests
// Test that the service can authenticate with PayPal OAuth and get an access token
test('it can get oauth token from paypal')->skip('Test needs implementation');

// Test that the service can verify a PayPal email address
test('it can verify paypal email')->skip('Test needs implementation');

// Test that the service can link a PayPal account to a referral enrollment and store the connection details
test('it can link paypal account to enrollment')->skip('Test needs implementation');

// Test that the service can disconnect a PayPal account and clear all PayPal-related data
test('it can disconnect paypal account')->skip('Test needs implementation');

// Test that attempting to create a payout without a connected/verified PayPal account throws an exception
test('it throws exception when creating payout without connected account')->skip('Test needs implementation');

// Test that the service can successfully create a payout to a connected PayPal account using the Payouts API
test('it can create payout with connected account')->skip('Test needs implementation');
