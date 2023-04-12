
	var script = window.document.createElement('script');
	script.src = window.document.getElementById('urlM1').textContent;
	window.document.head.appendChild(script);
	require.config({ paths: { vs: window.document.getElementById('urlM2').textContent } });
	var text = window.document.getElementById('textMonaco').innerHTML;
	text = text.replaceAll("&lt;", "<");
	text = text.replaceAll("&gt;", ">");
	require(['vs/editor/editor.main'], function () {
		window.editor = monaco.editor.create(document.getElementById('containerMonaco'), {
			value: text,
			language: window.document.getElementById('languageMonaco').innerHTML
		});
		window.editor.getModel().onDidChangeContent((event) => {
			let value = window.editor.getValue();
			window.document.getElementById(window.document.getElementById('monacoID').textContent).textContent = value;
		});
	
	});

	