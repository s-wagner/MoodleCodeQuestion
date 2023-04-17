export const init = ({ lang, url1, url2, text, mID, edit, intel, inline, keywords, variables, functions, classes, modules, tabsize}) => {
    var script = window.document.createElement('script');
    script.src = url1;
    window.document.head.appendChild(script);

    console.log(intel);
    console.log(inline);
    console.log(keywords);
    console.log(variables);
    console.log(functions);
    console.log(classes);
    console.log(modules);

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
            readOnly: !edit,
            tabSize: tabsize,
        });

        const suggestOptions = {
            showInlineDetails: inline,
            showKeywords: keywords,
            showVariables: variables,
            showFunctions: functions,
            showClasses: classes,
            showModules: modules,
        };

        window.editor.updateOptions({
            quickSuggestions: intel,
            suggestOnTriggerCharacters: intel,
            suggest: suggestOptions,
        });

        monaco.languages.registerDocumentFormattingEditProvider('java', {
            provideDocumentFormattingEdits(model, options) {
                var formatted = javaBeautifier(model.getValue());
                return [
                    {
                        range: model.getFullModelRange(),
                        text: formatted
                    }
                ];
            }
        });

        monaco.languages.registerDocumentFormattingEditProvider('sql', {
            provideDocumentFormattingEdits(model, options) {
                var formatted = sqlBeautifier(model.getValue());
                return [
                    {
                        range: model.getFullModelRange(),
                        text: formatted
                    }
                ];
            }
        });

        monaco.languages.registerDocumentFormattingEditProvider('python', {
            provideDocumentFormattingEdits(model, options) {
                var formatted = pythonBeautifier(model.getValue());
                return [
                    {
                        range: model.getFullModelRange(),
                        text: formatted
                    }
                ];
            }
        });

        monaco.languages.registerDocumentFormattingEditProvider('pascal', {
            provideDocumentFormattingEdits(model, options) {
                var formatted = beautifyPascal(model.getValue(), tabsize);
                return [
                    {
                        range: model.getFullModelRange(),
                        text: formatted
                    }
                ];
            }
        });

        function format() {
            window.editor.getAction('editor.action.formatDocument').run();
        }

        document.getElementById('formater').onclick = format;

        if (edit) {
            window.editor.getModel().onDidChangeContent((event) => {
                let value = window.editor.getValue();
                window.document.getElementById(mID).textContent = value;
            });
        }
    });
};


function javaBeautifier(javaCode) {
    const lines = javaCode.split('\n');
    let beautifiedCode = '';
    let indentLevel = 0;

    for (const line of lines) {
        let trimmedLine = line.trim();

        // Decrease indent level for closing braces.
        if (trimmedLine.startsWith('}')) {
            indentLevel--;
        }

        // Add appropriate indentation.
        for (let i = 0; i < indentLevel; i++) {
            beautifiedCode += '\t';
        }

        // Add the line with indentation.
        beautifiedCode += trimmedLine + '\n';

        // Increase indent level for opening braces.
        if (trimmedLine.endsWith('{')) {
            indentLevel++;
        }
    }

    return beautifiedCode.trim();
}

function sqlBeautifier(sqlCode) {
    const breakBeforeKeywords = [
        'FROM', 'WHERE', 'GROUP BY', 'HAVING', 'ORDER BY', 'JOIN', 'INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'ON',
        'INSERT INTO', 'VALUES', 'UPDATE', 'SET', 'DELETE FROM', 'CREATE TABLE', 'ALTER TABLE', 'DROP TABLE', 'UNION', 'UNION ALL',
    ];

    const regexStr = breakBeforeKeywords.map(kw => {
        const words = kw.split(' ');
        return words.map(w => `(?<=\\s|^)${w}(?=\\s|$)`).join('\\s+');
    }).join('|');

    const regex = new RegExp(regexStr, 'gi');

    let beautifiedCode = sqlCode.replace(regex, match => `\n${match}`);

    // Remove leading newline if present
    if (beautifiedCode.startsWith('\n')) {
        beautifiedCode = beautifiedCode.slice(1);
    }

    return removeBlankLines(beautifiedCode.trim());
}


function removeBlankLines(text) {
    return text.replace(/^\s*\n/gm, '');
}

function pythonBeautifier(pythonCode) {
    const lines = pythonCode.split('\n');
    let beautifiedCode = '';
    let indentLevel = 0;
    let consecutiveBlankLines = 0;

    for (const line of lines) {
        const trimmedLine = line.trim();

        if (trimmedLine) {
            // Check if the line starts with a de-indenting keyword.
            const deIndentKeywords = ['else', 'elif', 'except', 'finally'];
            const shouldDeIndent = deIndentKeywords.some(keyword => trimmedLine.startsWith(keyword));

            if (shouldDeIndent) {
                indentLevel--;
            }

            // Add appropriate indentation.
            const indentation = ' '.repeat(4 * indentLevel);
            beautifiedCode += indentation + trimmedLine + '\n';

            // Update the indent level.
            if (trimmedLine.endsWith(':')) {
                indentLevel++;
            }

            // Reset consecutive blank lines counter.
            consecutiveBlankLines = 0;
        } else {
            consecutiveBlankLines++;

            // Reset indent level after two consecutive blank lines.
            if (consecutiveBlankLines === 2) {
                indentLevel = 0;
            }

            // Preserve empty lines.
            beautifiedCode += '\n';
        }
    }

    return beautifiedCode;
}

function beautifyPascal(code, tabSize) {
    let indentLevel = 0;
    const lines = code.split("\n");
    let formattedLines = [];

    lines.forEach((line) => {
        const trimmedLine = line.trim().toLowerCase(); // convert to lowercase
        if (trimmedLine.length > 0) {
            // If line is not empty
            if (
                trimmedLine.startsWith("end") ||
                trimmedLine.startsWith("until") ||
                trimmedLine.startsWith("else")
            ) {
                // Dedent one level for end, until, and else
                indentLevel -= tabSize;
            }
            if(trimmedLine.startsWith("var")){
                indentLevel += tabSize;
            }

            formattedLines.push(
                ' '.repeat(indentLevel) + line.trim().replace(/\s+/g, " ")
            );

            if(trimmedLine.startsWith("var")){
                indentLevel -= tabSize;
            }

            if (
                trimmedLine.endsWith("begin") ||
                trimmedLine.endsWith("then") ||
                trimmedLine.includes("begin (*") ||
                trimmedLine.includes("then (*") ||
                trimmedLine.includes("begin //") ||
                trimmedLine.includes("then //")
            ) {
                // Indent one level for begin and then
                indentLevel += tabSize;
            }
        }
    });

    return formattedLines.join("\n");
}