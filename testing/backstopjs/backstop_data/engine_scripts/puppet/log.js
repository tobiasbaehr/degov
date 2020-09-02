/**
 * @file
 */

module.exports = (message, color = null) => {
  const colors = {
    green: '\x1b[32m',
    gray: '\x1b[90m',
    grey: '\x1b[90m',
    red: '\x1b[31m',
    blue: '\x1b[34m',
    reset: "\x1b[0m",
  };
  if (color && colors[color]) {
    console.log(colors[color] + '%s' + colors.reset, message.toString());
  }
  else {
    console.log(message);
  }
}
