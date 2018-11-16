/**
 *
 * @param connection connection
 */
let navigatorCLient = function(connection) {
  this.NetworkInformation = {};
  this.NetworkInformation.connection = connection || {};
};

navigatorCLient.prototype.getConnection = function () {
  return this.NetworkInformation.connection;
}

export default navigatorCLient;




