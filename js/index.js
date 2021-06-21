function Delete(id)
{
	if ( confirm('Вы уверены?') )
	{
		let xhr = new XMLHttpRequest();
		xhr.open('GET', `/delete/?id=${id}`);
		xhr.send();
	}
}