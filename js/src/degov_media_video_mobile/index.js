var navigator = {};
navigator.connection = {};
navigator.connection =  {
  'onchange':null,
  'effectiveType': "4g",
  'rtt': 50,
  'downlink':10,
  'saveDate': false
};



var navi = function() {
  this.navigator = navigator;
};

navi.prototype.getConnection = function() {
  let connection;

  if (typeof this.navigator.connection !== 'undefined') {
    connection = this.navigator.connection;
  } else if (typeof this.navigator.mozConnection !== 'undefined') {
    connection = this.navigator.mozConnection;
  } else if (typeof this.navigator.webkitConnection !== 'undefined') {
    connection = this.navigator.webkitConnection;
  }

  return connection;
};



var speed_checker = function() {

};
speed_checker.prototype.checkSlowLoadTime = function() {
  const maxCellularLoadTime = 2000;
  let loadTime = new Date().valueOf() - window.performance.timing.requestStart;
  return (loadTime > maxCellularLoadTime);
};

module.exports = {
  navi,speed_checker
};
