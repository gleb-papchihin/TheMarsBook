let bad = document.getElementById('bad');
let middle = document.getElementById('middle');
let perfect = document.getElementById('perfect');
let display = document.getElementById('rank_display');

function Bad()
{
	bad.setAttribute('style', 'background: #BBF18E;');
	middle.setAttribute('style', 'background: #f6f6f6;');
	perfect.setAttribute('style', 'background: #f6f6f6;');
}

function Middle()
{
	bad.setAttribute('style', 'background: #BBF18E;');
	middle.setAttribute('style', 'background: #BBF18E;');
	perfect.setAttribute('style', 'background: #f6f6f6;');
}

function Perfect()
{
	bad.setAttribute('style', 'background: #BBF18E;');
	middle.setAttribute('style', 'background: #BBF18E;');
	perfect.setAttribute('style', 'background: #BBF18E;');
}

function Change()
{
	let formData = new FormData(document.forms.estimate);
	let rank = formData.get('rank');
	let xhr = new XMLHttpRequest();
	let url = window.location.href;
	xhr.open('GET', `${url}&rank=${rank}`);
	xhr.send();
}