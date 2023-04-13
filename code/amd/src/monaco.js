export const init = ({lang, url1, url2, text, mID, edit}) => {
    var script = window.document.createElement('script');
    script.src = url1;
    window.document.head.appendChild(script);
        
    text = text.replaceAll("&lt;", "<");
    text = text.replaceAll("&gt;", ">");
    text = text.replaceAll("&amp;", "&");
    text = text.replaceAll("&quot;", "\"");
    text = text.replaceAll("&#039;", "\'");
    text = text.replaceAll("&apos;", "\'");

    require.config({ paths: { vs: url2 } });
    require(['vs/editor/editor.main'], function () {
        window.editor = monaco.editor.create(document.getElementById('containerMonaco' + mID), {
            value: text,
            language: lang,
            readOnly: !edit
        });

        if(edit){
            window.editor.getModel().onDidChangeContent((event) => {
                let value = window.editor.getValue();
                window.document.getElementById(mID).textContent = value;
            });
        }
    });
};