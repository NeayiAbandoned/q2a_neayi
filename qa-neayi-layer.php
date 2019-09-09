<?php
/*
	Ajax Images by Bertrand Gorge, Neayi
	https://neayi.com/

	File: qa-plugin/ajax-images/qa-ai-layer.php
	Description: Displays the images and upload buttons, as well as the JS scripts and CSS

	MIT License

	Copyright (c) 2019 neayi

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.

*/

class qa_html_theme_layer extends qa_html_theme_base
{
	function head_script() {
		qa_html_theme_base::head_script();

        $this->output('<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css" />');


    }

    function body_hidden()
    {
        qa_html_theme_base::body_hidden();

        $this->output('<script src="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.js" data-cfasync="false"></script>');
        $this->output('<script>');
        $this->output('window.cookieconsent.initialise({');
        $this->output('  "palette": {');
        $this->output('    "popup": {');
        $this->output('      "background": "#237afc"');
        $this->output('    },');
        $this->output('    "button": {');
        $this->output('      "background": "#fff",');
        $this->output('      "text": "#237afc"');
        $this->output('    }');
        $this->output('  },');
        $this->output('  "theme": "classic",');
        $this->output('  "position": "bottom-left",');
        $this->output('  "content": {');
        $this->output('    "message": "Ce site utilise des cookies pour permettre son fonctionnement et vous assurer de la meilleure expérience possible.",');
        $this->output('    "dismiss": "C\'est compris !",');
        $this->output('    "link": "En savoir plus",');
        $this->output('    "href": "https://questions.tripleperformance.fr/politique-de-confidentialit%C3%A9"');
        $this->output('  }');
        $this->output('});');
        $this->output('</script>');
    }
}