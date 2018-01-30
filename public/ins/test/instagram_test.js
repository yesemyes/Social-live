(function($) {

  module('jQuery#instagram', {
    setup: function() {
      this.elem = $('#qunit-fixture');
    }
  });

  test('throws exception if clientId nor accessToken is set', function() {
    throws(
      function() {
        this.elem.instagram();
      },
      /You must provide an access token or a client id/,
      'should throw exception'
    );
  });

  test('throws exception if userId request and accessToken is not set', function() {
    throws(
      function() {
        this.elem.instagram({userId: '2568395090', clientId: '7502c314346d4f2fac1d02cbe9074c71'});
      },
      /You must provide an access token/,
      'should throw exception'
    );
  });

  asyncTest('is chainable', function() {
    expect(5);
    this.elem.on('didLoadInstagram', function(event, response) {
      ok(true, '"didLoadInstagram" event was triggered');
      ok(typeof response === 'object', 'should get response as param');
      start();
    });
    this.elem.on('willLoadInstagram', function(event, options) {
      ok(true, '"willLoadInstagram" event was triggered');
      strictEqual(options.clientId, '7502c314346d4f2fac1d02cbe9074c71', 'should get options as param');
    });
    strictEqual(this.elem.instagram({clientId: '7502c314346d4f2fac1d02cbe9074c71'}), this.elem, 'should be chainable');
  });

}(jQuery));
