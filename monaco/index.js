import * as monaco from 'monaco-editor';
import { toSocket, WebSocketMessageReader, WebSocketMessageWriter } from 'vscode-ws-jsonrpc';
import {
  MonacoLanguageClient,
  MonacoServices,
} from 'monaco-languageclient';
import { CloseAction, ErrorAction } from 'vscode-languageclient';

var mID = document.getElementById('mId').textContent;
var edit = document.getElementById('edit').textContent === '1';
var intel = document.getElementById('intel').textContent === '1';
var inline = document.getElementById('inline').textContent === '1';
var keywords = document.getElementById('keywords').textContent === '1';
var variables = document.getElementById('variables').textContent === '1';
var functions = document.getElementById('functions').textContent === '1';
var classes = document.getElementById('classes').textContent === '1';
var modules = document.getElementById('modules').textContent === '1';

var language = document.getElementById('lang').textContent;
var text = document.getElementById('text').textContent;

var tabsize = Number(document.getElementById('tabsize').textContent);

text = text.replaceAll("&lt;", "<");
text = text.replaceAll("&gt;", ">");
text = text.replaceAll("&amp;", "&");
text = text.replaceAll("&quot;", "\"");
text = text.replaceAll("&#039;", "\'");
text = text.replaceAll("&apos;", "\'");

if (language == 'java') {
  var ext = '.java';
} else if (language == 'csharp') {
  var ext = '.cs';
} else if (language == 'pascal') {
  var ext = '.pas';
} else if (language == 'python') {
  var ext = '.py';
} else if (language == 'c'){
  var ext = '.c';
} else if (language == 'cpp') {
  var ext = '.cpp'
}

var ur = monaco.Uri.parse('file:///tmp/demo' + ext);
if(language == 'csharp'){
  ur = monaco.Uri.parse('file:///app/demo' + ext);
}
//ur.path = 'c:/Users/p61580/Desktop/nodeexample/demo.java';
console.log(ur.toString());

const editor = monaco.editor.create(document.getElementById('containerMonaco' + mID), {
  model: monaco.editor.createModel(text, language, ur),
  language: language,
  readOnly: !edit,
  tabSize: tabsize,
});


const suggestOptions = {
  showInlineDetails: inline,
  showKeywords: keywords,
  showVariables: variables,
  showFunctions: functions,
  showClasses: classes,
  showInterfaces: classes,
  showModules: modules,
};

editor.updateOptions({
  quickSuggestions: intel,
  suggestOnTriggerCharacters: intel,
  suggest: suggestOptions,
});

if (edit) {

  editor.getModel().onDidChangeContent((event) => {
    let value = editor.getValue();
    document.getElementById(mID).textContent = value;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'http://localhost:3002/add' + language, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify({
      value: value
    }));
  });

  MonacoServices.install();

  function createLanguageClient(connection) {
    return new MonacoLanguageClient({
      name: 'Java Language Client',
      clientOptions: {
        documentSelector: [language],
        errorHandler: {
          error: () => ({ action: ErrorAction.Continue }),
          closed: () => ({ action: CloseAction.DoNotRestart })
        }
      },

      connectionProvider: {
        get: () => {
          return Promise.resolve(connection);
        },
      },
    });
  }

  function createWebSocket(url) {
    const webSocket = new WebSocket(url);
    webSocket.onopen = () => {
      const socket = toSocket(webSocket);
      const reader = new WebSocketMessageReader(socket);
      const writer = new WebSocketMessageWriter(socket);
      const languageClient = createLanguageClient({
        reader,
        writer
      });
      languageClient.start();
    };
    return webSocket;
  }

  const webSocketUrl = 'ws://localhost:3001/' + language;
  const webSocket = createWebSocket(webSocketUrl);

  window.onbeforeunload = () => {
    // On page reload/exit, close web socket connection
    webSocket?.close();
  };
}

