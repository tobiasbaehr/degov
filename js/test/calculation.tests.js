import sinon from "sinon";
import sum from '../src/common-functions/sum';


QUnit.test("Test Calculation", function (assert){
  var value = 2;
  assert.equal(value, new sum(1,1).add(), "We expect value to be 2");

});
