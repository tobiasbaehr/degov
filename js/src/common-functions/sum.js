/**
 * @param X x
 * @param Y y
 */

let sum = function(x, y) {
    this.x = x || 1;
    this.y = y || 1;

};

sum.prototype.add = function() {
    return (this.x + this.y);
};

export default sum;
