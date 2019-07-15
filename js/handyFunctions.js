

function setTextToElement(_element, _text) {
  if (!_element) return console.error("- setTextToElement: the element (", _element, ") doesn't exist.");
  _element.innerHTML = "";
  let a = document.createElement('a');
  a.text = String(_text);
  _element.append(a);
}

function inArray(arr, item) {
  for (let i = 0; i < arr.length; i++)
  {
    if (arr[i] == item)
    {
      return true;
    }
  }
  return false;
}