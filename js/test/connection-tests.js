import Navi from '../../modules/degov_media_video_mobile/js/navi';

QUnit.test("Test type of Connections", function(assert) {
  const navigator =  {
    connection: {
      'onchange':null,
      'effectiveType': "4g",
      'rtt': 50,
      'downlink':10,
      'saveDate': false
    }
  };

  const navi = new Navi(navigator);
  navi.getConnection();

  assert.equal('undefined', typeof navi.getConnection().type, "We expect value to be undefined");
  assert.ok(typeof navi.getConnection().type,"must be undefined");
  assert.ok(typeof navi.getConnection().effectiveType !== 'undefined', 'must be false');
});
