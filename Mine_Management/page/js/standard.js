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

sizeSelector.change(function sizeSelectorChange() {
  planetEditor.find('#map').empty();
  var table = jQuery('<table style="width: 100%;"></table>');
  planetEditor.find('#map').append(table);
  for (i = 0; i < sizeSelector.val(); i++) {
	var row = jQuery('<tr></tr>');
	table.append(row);
	for (i2 = 0; i2 < sizeSelector.val(); i2++) {
	  var cell = jQuery('<td></td>');
	  row.append(cell);
	  var ddl = jQuery('<select style="width: 100%;" name="planetGrid[' + i + '][' + i2 + ']"></select>');
	  cell.append(ddl);
	  for (i3 = 0, l = terrainTypes.length; i3 < l; i3++) {
		var option = jQuery('<option></option>');
		option.text(terrainTypes[i3].name);
		option.val(terrainTypes[i3].id);
		option.attr('data-image', terrainTypes[i3].image);
		ddl.append(option);
	  }
	  ddl.change(function terrainSelectorChange() {
		jQuery(this).css('background-image', 'url(' + jQuery(this).find(':selected').attr('data-image') + ')');
		  });
		ddl.trigger('change');
		}
		}
		});

jQuery('#manageplanets').click(managePlanetsClick);

function renderPlanet(planet, index) {
  var workingPlanetTemplate = planetTemplate.clone();
  workingPlanetTemplate.find('#name').text(planet.name);
  workingPlanetTemplate.removeClass('invisible');
  content.append(workingPlanetTemplate);
  var table = workingPlanetTemplate.children('#map').append('<table></table>').children('table'),
	  node = 0;
  for (wHeight = 0, height = planet.height; wHeight < height; wHeight++) {
	var row = jQuery('<tr></tr>');
	table.append(row);
	for (wWidth = 0, width = planet.width; wWidth < width; wWidth++) {
	  var cell = jQuery('<td></td>'),
		img = jQuery('<img />').attr('src', terrainTypes[planet.terrain[wHeight][wWidth].terrainid - 1].image).css('width', '100%').css('height', 'auto');
	  row.append(cell);
	  cell.append(img);
	}
  }
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
	console.log(planet);
	planetEditor.find('#name').val(planet.name);
	planetEditor.find('#size').val(planet.width);
	planetEditor.find('#size').trigger('change');
	for (i = 0, l = planet.width; i < l; i++) {
	  for (i2 = 0; i2 < l; i2++) {
		planetEditor.find('[name="planetGrid[' + i + '][' + i2 + ']"]').val(planet.terrain[i][i2].terrainid);
		planetEditor.find('[name="planetGrid[' + i + '][' + i2 + ']"]').trigger('change');
	  }
	}
	planetEditor.find('#planetid').val(planet.id);
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

