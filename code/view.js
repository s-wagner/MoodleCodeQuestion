
	var script = window.document.createElement('script');
	script.src = window.document.getElementById('urlM1').textContent;
	window.document.head.appendChild(script);
	require.config({ paths: { vs: window.document.getElementById('urlM2').textContent } });
	require(['vs/editor/editor.main'], function () {
		window.editor = monaco.editor.create(document.getElementById('containerMonaco'), {
			value: window.document.getElementById('textMonaco').innerHTML,
			language: window.document.getElementById('languageMonaco').innerHTML,
			readOnly: true
		});
	
	});

	