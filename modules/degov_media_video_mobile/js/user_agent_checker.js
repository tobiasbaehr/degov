const UserAgentChecker = function(window) {
  this.window = window;
};

UserAgentChecker.prototype.isMobile = function() {
  return window.navigator.userAgent.search(/mobile/i) !== -1;
};

export default UserAgentChecker;
