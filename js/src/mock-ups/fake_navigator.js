const FakeNavigator = function() {
  this.navigator = {};
  this.navigator.connection = {};
};

FakeNavigator.prototype.setConnection = function(conn) {

  /*'onchange':null,
    'effectiveType': "4g",
    'rtt': 50,
    'downlink':10,
    'saveDate': false*/

  this.navigator.connection = conn.onchange || null;
  this.navigator.connection = conn.effectiveType || "4g";
  this.navigator.connection = conn.rtt || 50;
  this.navigator.connection = conn.downlink || 10;
  this.navigator.connection = conn.saveDate || false;


};

FakeNavigator.prototype.getConnection = function () {
  return this.navigator.connection;
}

export default FakeNavigator;
