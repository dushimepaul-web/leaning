const fs=require('fs');
const c=fs.readFileSync('C:\\wamp64\\www\\leaning\\application\\modules\\Notes\\views\\bulletins.php','utf8');
const m=c.match(/<script>([\s\S]*?)<\/script>/);
if(m) fs.writeFileSync('C:\\wamp64\\www\\leaning\\test_js.js', m[1]);
