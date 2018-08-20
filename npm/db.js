var db = require('../node/database.js');

var port = 3000;
db.registerKeepAllive();

/**
 * start server and register event for receiving post call from SeleniumSingleTestRunner after test execution started or finished
 * @param request
 * @param response
 */
var server = require('http').createServer(function (request, response) {
}).listen(port);

require('socket.io').listen(server);
console.log("Server running on port " + port);


var mysql = require('mysql');
var config = require('../node/config.json');
var con1 = mysql.createConnection(config);

con1.connect(function () {
    console.log('connection established');

});

exports.registerKeepAllive = function () {
    setInterval(function () {
        con1.query('SELECT 1');
        console.log('keep allive');
    }, 60000);
    return this;
};

con1.query('SELECT * FROM timemeasure WHERE shown = 0', function (err, result) {
    if (err) {
        console.log(err);
        throw err;
    }
    console.log(result);
});

