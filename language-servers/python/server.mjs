import { WebSocketServer } from 'ws';
import { URL } from 'url';
import express, { json } from 'express';
import cors from 'cors';
import fs from 'fs';
import { WebSocketMessageReader, WebSocketMessageWriter } from 'vscode-ws-jsonrpc';
import { dirname } from 'path';
import { fileURLToPath } from 'url';
import { exec } from 'child_process';
process.on('uncaughtException', function (err) {
    console.error('Uncaught Exception: ', err.toString());
    if (err.stack) {
        console.error(err.stack);
    }
});

function sanitizeText(txt) {
    txt = txt.replaceAll('ä', 'ae');
    txt = txt.replaceAll('ö', 'oe');
    txt = txt.replaceAll('ü', 'ue');
    //txt = txt.replaceAll('\"', '\'');
    return txt;
}

function getLocalDirectory() {
    const __filename = fileURLToPath(import.meta.url);
    return dirname(__filename);
}


var jsonParser = json();
// create the express application
const app = express();
// server the static content, i.e. index.html
app.use(express.static(getLocalDirectory()));
// start the server
const server = app.listen(3001);

const app2 = express();
app2.use(cors());

app2.post('/addjava', jsonParser, function (req, res) {

    fs.unlink('/tmp/demo.java', function (err) {

        fs.appendFile('/tmp/demo.java', sanitizeText(req.body.value), function (err2) {
        });
    });

    res.sendStatus(200);
});

app2.post('/addcsharp', jsonParser, function (req, res) {
    fs.unlink('/home/p61580/demo.cs', function (err) {
        fs.appendFile('/home/p61580/demo.cs', sanitizeText(req.body.value), function (err2) {
        });
    });

    res.sendStatus(200);
});

app2.post('/addpascal', jsonParser, function (req, res) {
    fs.unlink('/tmp/demo.pas', function (err) {

        fs.appendFile('/tmp/demo.pas', sanitizeText(req.body.value), function (err2) {
        });
    });

    res.sendStatus(200);
});

app2.post('/addpython', jsonParser, function (req, res) {
    fs.unlink('/tmp/demo.py', function (err) {

        fs.appendFile('/tmp/demo.py', sanitizeText(req.body.value), function (err2) {
        });
    });

    res.sendStatus(200);
});

app2.post('/addc', jsonParser, function (req, res) {
    fs.unlink('/tmp/demo.c', function (err) {

        fs.appendFile('/tmp/demo.c', sanitizeText(req.body.value), function (err2) {
        });
    });

    res.sendStatus(200);
});

const server2 = app2.listen(3002);

// create the web socket
const wss = new WebSocketServer({
    noServer: true,
    perMessageDeflate: false
});
server.on('upgrade', (request, socket, head) => {
    const baseURL = `http://${request.headers.host}/`;
    const pathname = request.url ? new URL(request.url, baseURL).pathname : undefined;
    var process;
    // WINDOwS
    /*if(pathname === '/java'){
        process = exec('java -Declipse.application=org.eclipse.jdt.ls.core.id1 -Dosgi.bundles.defaultStartLevel=4  -Declipse.product=org.eclipse.jdt.ls.core.product -Dlog.level=ALL  -Xmx1G --add-modules=ALL-SYSTEM --add-opens java.base/java.util=ALL-UNNAMED --add-opens java.base/java.lang=ALL-UNNAMED -jar C:\\Users\\p61580\\Desktop\\jdt-language-server-1.9.0-202203031534\\plugins\\org.eclipse.equinox.launcher_1.6.400.v20210924-0641.jar  -configuration C:\\Users\\p61580\\Desktop\\jdt-language-server-1.9.0-202203031534\\config_win -data C:\\Users\\p61580\\Desktop\\Data');
    }else if(pathname === '/csharp'){
        process =  exec('C:\\Users\\p61580\\Desktop\\csharp-language-server-0.7.1\\src\\CSharpLanguageServer\\bin\\Release\\net7.0\\CSharpLanguageServer.exe');
    }*/
    // LINUX
    if (pathname === '/java') {
        var process1 = exec('java -Declipse.application=org.eclipse.jdt.ls.core.id1 -Dosgi.bundles.defaultStartLevel=4  -Declipse.product=org.eclipse.jdt.ls.core.product -Dlog.level=ALL  -Xmx1G --add-modules=ALL-SYSTEM --add-opens java.base/java.util=ALL-UNNAMED --add-opens java.base/java.lang=ALL-UNNAMED -jar ./server/jdt-language-server-1.9.0-202203031534/plugins/org.eclipse.equinox.launcher_1.6.400.v20210924-0641.jar  -configuration ./server/jdt-language-server-1.9.0-202203031534/config_linux -data ./Data');
    } else if (pathname === '/csharp') {
        var process1 = exec('/mnt/c/Users/p61580/Desktop/nodeexample/server/csharp-language-server/src/CSharpLanguageServer/bin/Debug/net7.0/CSharpLanguageServer');
    } else if (pathname === '/pascal') {
        var process1 = exec('/home/p61580/npas2/pascal-language-server/server/lib/x86_64-linux/pasls')
    } else if (pathname === '/python') {
        var process1 = exec('pylsp');
    } else if (pathname === '/c') {
        var process1 = exec('ccls');
    }
    wss.handleUpgrade(request, socket, head, webSocket => {
        const socket = {
            send: content => webSocket.send(content, error => {
                if (error) {
                    throw error;
                }
            }),
            onMessage: cb => webSocket.on('message', (data) => {
                cb(data);
            }),
            onError: cb => webSocket.on('error', cb),
            onClose: cb => webSocket.on('close', () => {
                process.kill();
                cb();
            }),
            dispose: () => { webSocket.close() }
        };
        let reader = new WebSocketMessageReader(socket);
        let writer = new WebSocketMessageWriter(socket);
        // launch the server when the web socket is opened
        if (webSocket.readyState === webSocket.OPEN) {
            launch(process1, reader, writer, pathname);
        } else {
            webSocket.on('open', () => launch(process1, reader, writer, pathname));
        }

    });
});

var arr = []

function launch(javaProcess, reader, writer, lang) {

    javaProcess.stderr.on('data', (data) => {
        console.log(data);
    });

    reader.listen((data) => {
        let inText = sanitizeText(JSON.stringify(data));
        let input = `Content-Length: ${inText.length}\r\n\r\n` + inText;
        javaProcess.stdin.write(input);
    });

    javaProcess.stdout.on('data', (data) => {
        
        data = data.replaceAll('Content-Type: application/vscode-jsonrpc; charset=utf8', '');
        data = data.replaceAll(/Content-Length: [0-9]*/g, 'Content-Length');
        let lines = data.split('Content-Length');
        for (let line of lines) {
            if (line.includes('{')) {
                if(!arr[0]){
                    try{
                        let json = JSON.parse(line);
                        writer.write(json);
                    }catch (error) {
                        arr.push(line);
                    }
                } else {
                    arr.push(line);
                    let textJson = '';
                    for (let part of arr) {
                        textJson += part;
                    }
                    try{
                        let json = JSON.parse(textJson);
                        writer.write(json);
                        arr = [];
                    }catch (error) {
                        console.log(error);
                        console.log(arr.length);
                    }
                }
                
            }
        }


        /*for (let line of lines) {
            if (line.includes('{')) {
                if (line.includes('Content-Length')) {
                    let nLines = line.split('Content-Length');
                    for (let nLine of nLines) {
                        if (nLine.includes('{')) {
                            try {
                                console.log(nLine);
                                var json = JSON.parse(nLine);
                                writer.write(json);
                            } catch (error) {
                                arr.push(nLine);
                                if (arr[1] != '') {
                                    console.log(arr[0] + arr[1]);
                                    var json = JSON.parse(arr[0] + arr[1]);
                                    arr = [];
                                    writer.write(json);
                                }

                            }

                        }
                    }
                } else {
                    try {
                        console.log(line);
                        var json = JSON.parse(line);
                        writer.write(json);
                    } catch (error) {
                        arr.push(line);
                        if (arr[1] != null) {
                            console.log(arr[0] + arr[1]);
                            var json = JSON.parse(arr[0] + arr[1]);
                            arr = [];
                            writer.write(json);
                        }


                    }
                }
            }
        }*/


    });
}