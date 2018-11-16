var speed_checker = function() {

};
speed_checker.prototype.checkSlowLoadTime = function() {
  const maxCellularLoadTime = 2000;
  let loadTime = new Date().valueOf() - window.performance.timing.requestStart;
  return (loadTime > maxCellularLoadTime);
};
