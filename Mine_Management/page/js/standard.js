var planetTemplate = jQuery('#planetTemplate'),
	content = jQuery('#content'),
	planetEditor = jQuery('#planetEditor'),
	sizeSelector = planetEditor.find('#size'),
	terrainTypes = null,
	planets = null;

jQuery.ajax({
  url: '?q=/terraintypes',
  complete: function terrainTypesDownloadComplete(context) {
	terrainTypes = jQuery.parseJSON(context.responseText);
	sizeSelector.trigger('change');
  }
});

for (i = 1; i <= 20; i++) {
  sizeSelector.append(jQuery('<option></option>').val(i).text(i));
}

function addPlanetClick () {
  planetEditor.removeClass('invisible');
  content.addClass('invisible');
}

jQuery('#addplanet').click(addPlanetClick);
//Handles the change of size drop down.
function sizeSelectorChange() {
  planetEditor.find('#map').empty();
  var planet = null;
  if (jQuery('#index').val() != '') {
	planet = planets[jQuery('#index').val()];
  }

  var table = jQuery('<table style="width: 100%; max-width: ' + (sizeSelector.val() * 50) + 'px;"></table>');
  planetEditor.find('#map').append(table);
  //Iterates the current size and creates table the appropriate size. There's probably a better way to handle this. It doesn't
  //appear to be properly caching the images which leads to a slow load time for this.
  for (i = 0; i < sizeSelector.val(); i++) {
	var row = jQuery('<tr></tr>');
	table.append(row);
	for (i2 = 0; i2 < sizeSelector.val(); i2++) {
	  var cell = jQuery('<td></td>');
	  cell.css({'max-width': '50px', 'max-height': '50px', 'width': (table.width() / sizeSelector.val()) + 'px'});
	  cell.css({'height': cell.width() + 'px'});
	  row.append(cell);
	  var ddl = jQuery('<select style="width: 100%; height: 100%;" name="planetGrid[' + i + '][' + i2 + ']"></select>');
	  cell.append(ddl);
	  //Generating the terrain type selectors.
	  for (i3 = 0; i3 < terrainTypes.length; i3++) {
		var option = jQuery('<option></option>');
		option.text(terrainTypes[i3].name);
		option.val(terrainTypes[i3].id);
		option.attr('data-image', terrainTypes[i3].image);
		ddl.append(option);
	  }
	  //Creates the terrain type selector drop down listener.
	  ddl.change(function terrainSelectorChange() {
		jQuery(this).css('background-image', 'url(' + jQuery(this).find(':selected').attr('data-image') + ')');
	  });
	  //If the planet is set and the terrain location isn't undefined, we need to set the terrain type appropriately.
	  if (planet != null && planet.terrain[i] !== undefined && planet.terrain[i][i2] !== undefined) {
		ddl.val(planet.terrain[i][i2].terrainid);
	  }
	  ddl.css({'width': '100%', 'height': '100%'});
	  ddl.trigger('change');
	}
  }
}
sizeSelector.change(sizeSelectorChange);
//We add the managePlanetsClick event listener.
jQuery('#manageplanets').click(managePlanetsClick);
//This function is used for rendering the planet on the manage planets page.
function renderPlanet(planet, index) {
  var workingPlanetTemplate = planetTemplate.clone();
  workingPlanetTemplate.find('#name').text(planet.name);
  workingPlanetTemplate.removeClass('invisible');
  content.append(workingPlanetTemplate);
  //We're going to start generating the table that contains the planet definition.
  var table = workingPlanetTemplate.children('#map').append('<table></table>').children('table'),
	  node = 0;
  //We need to iterate over the height and width of the planet.
  for (height = 0; height < planet.height; wHeight++) {
	var row = jQuery('<tr></tr>');
	table.append(row);
	for (width = 0; width < planet.width; wWidth++) {
	  var cell = jQuery('<td></td>'),
		img = jQuery('<img />').attr('src', terrainTypes[planet.terrain[height][width].terrainid - 1].image).css('width', '100%').css('height', 'auto');
	  row.append(cell);
	  cell.append(img);
	}
  }
  //We need to set the index position.
  workingPlanetTemplate.children('#index').val(index);
  workingPlanetTemplate.children('#delete').click(function deletePlanet() {
	jQuery.ajax({
	  accepts: 'application/json',
	  async: true,
	  url: '?q=/planets/delete/' + planet.id,
	  complete: managePlanetsDeleteComplete
	});
  });
  workingPlanetTemplate.children('#edit').click(function editPlanet() {
	var planet = planets[jQuery(this).parent().children('#index').val()];
	planetEditor.find('#name').val(planet.name);
	planetEditor.find('#size').val(planet.width);
	planetEditor.find('#planetid').val(planet.id);
	planetEditor.find('#size').trigger('change');
	addPlanetClick();
  });
}

function managePlanetsDeleteComplete(context, status) {
  var success = jQuery.parseJSON(context.responseText);
  if (success[0]) {
	jQuery(this).remove();
  }
}

function managePlanetsClick() {
  jQuery.ajax({
	accepts: 'application/json',
  async: true,
  url: '?q=/planets/return/all',
  complete: managePlanetsDownloadComplete
  });
}

function managePlanetsDownloadComplete(context, status) {
  content.empty();
  content.removeClass('invisible');
  planetEditor.addClass('invisible');
  planets = jQuery.parseJSON(context.responseText);
  for (i = 0, l = planets.length; i < l; i++) {
	renderPlanet(planets[i], i);
  }
}

