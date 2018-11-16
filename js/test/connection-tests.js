import sinon from "sinon";
import sum from "../src/common-functions/sum";
import navi_client from "../src/client/navigator-client-prototype";
//var navigator = require("../src/client/FakeNavigator");
//import navi from "../src/index"
//var navi = require('../../modules/degov_media_video_mobile/js/get_connection.js');
//import navi from "../src/index";
var expClasses = require('../src/degov_media_video_mobile/index');

QUnit.test("Test type of Connections", function(assert) {

  var navi = new expClasses.navi();

  assert.equal('undefined', typeof navi.getConnection().type, "We expect value to be undefined");
  assert.ok(typeof navi.getConnection().type,"must be undefined");
  assert.ok(typeof navi.getConnection().effectiveType !== 'undefined', 'must be false');
  /*assert.ok(typeof navi.getConnection().type,"must be undefined");
  assert.ok(typeof navi.getConnection().effectiveType !== 'undefined', 'must be false');*/

});

