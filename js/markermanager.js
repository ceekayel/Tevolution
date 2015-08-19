/**
 * @name MarkerManager v3
 * @version 1.0
 * @copyright (c) 2007 Google Inc.
 * @author Doug Ricket, Bjorn Brala (port to v3), others,
 *
 * @fileoverview Marker manager is an interface between the map and the user,
 * designed to manage adding and removing many points when the viewport changes.
 * <br /><br />
 * <b>How it Works</b>:<br/> 
 * The MarkerManager places its markers onto a grid, similar to the map tiles.
 * When the user moves the viewport, it computes which grid cells have
 * entered or left the viewport, and shows or hides all the markers in those
 * cells.
 * (If the users scrolls the viewport beyond the markers that are loaded,
 * no markers will be visible until the <code>EVENT_moveend</code> 
 * triggers an update.)
 * In practical consequences, this allows 10,000 markers to be distributed over
 * a large area, and as long as only 100-200 are visible in any given viewport,
 * the user will see good performance corresponding to the 100 visible markers,
 * rather than poor performance corresponding to the total 10,000 markers.
 * Note that some code is optimized for speed over space,
 * with the goal of accommodating thousands of markers.
 */
/*
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License. 
 */
/**
 * @name MarkerManagerOptions
 * @class This class represents optional arguments to the {@link MarkerManager}
 *     constructor.
 * @property {Number} maxZoom Sets the maximum zoom level monitored by a
 *     marker manager. If not given, the manager assumes the maximum map zoom
 *     level. This value is also used when markers are added to the manager
 *     without the optional {@link maxZoom} parameter.
 * @property {Number} borderPadding Specifies, in pixels, the extra padding
 *     outside the map's current viewport monitored by a manager. Markers that
 *     fall within this padding are added to the map, even if they are not fully
 *     visible.
 * @property {Boolean} trackMarkers=false Indicates whether or not a marker
 *     manager should track markers' movements. If you wish to move managed
 *     markers using the {@link setPoint}/{@link setLatLng} methods, 
 *     this option should be set to {@link true}.
 */
/**
 * Creates a new MarkerManager that will show/hide markers on a map.
 *
 * Events:
 * @event changed (Parameters: shown bounds, shown markers) Notify listeners when the state of what is displayed changes.
 * @event loaded MarkerManager has succesfully been initialized.
 *
 * @constructor
 * @param {Map} map The map to manage.
 * @param {Object} opt_opts A container for optional arguments:
 *   {Number} maxZoom The maximum zoom level for which to create tiles.
 *   {Number} borderPadding The width in pixels beyond the map border,
 *                   where markers should be display.
 *   {Boolean} trackMarkers Whether or not this manager should track marker
 *                   movements.
 */
function MarkerManager(map, opt_opts) {
  var me = this;
  me.map_ = map;
  me.mapZoom_ = map.getZoom();
  
  me.projectionHelper_ = new ProjectionHelperOverlay(map);
  google.maps.event.addListener(me.projectionHelper_, 'ready', function () {
    me.projection_ = this.getProjection();
    me.initialize(map, opt_opts);
  });
}
  
MarkerManager.prototype.initialize = function (map, opt_opts) {
  var me = this;
  
  opt_opts = opt_opts || {};
  me.tileSize_ = MarkerManager.DEFAULT_TILE_SIZE_;
  var mapTypes = map.mapTypes;
  // Find max zoom level
  var mapMaxZoom = 1;
  for (var sType in mapTypes ) {
    if (typeof map.mapTypes.get(sType) === 'object' && typeof map.mapTypes.get(sType).maxZoom === 'number') {
      var mapTypeMaxZoom = map.mapTypes.get(sType).maxZoom;
      if (mapTypeMaxZoom > mapMaxZoom) {
        mapMaxZoom = mapTypeMaxZoom;
      }
    }
  }
  
  me.maxZoom_  = opt_opts.maxZoom || 19;
  me.trackMarkers_ = opt_opts.trackMarkers;
  me.show_ = opt_opts.show || true;
  var padding;
  if (typeof opt_opts.borderPadding === 'number') {
    padding = opt_opts.borderPadding;
  } else {
    padding = MarkerManager.DEFAULT_BORDER_PADDING_;
  }
  // The padding in pixels beyond the viewport, where we will pre-load markers.
  me.swPadding_ = new google.maps.Size(-padding, padding);
  me.nePadding_ = new google.maps.Size(padding, -padding);
  me.borderPadding_ = padding;
  me.gridWidth_ = {};
  me.grid_ = {};
  me.grid_[me.maxZoom_] = {};
  me.numMarkers_ = {};
  me.numMarkers_[me.maxZoom_] = 0;
  google.maps.event.addListener(map, 'dragend', function () {
    me.onMapMoveEnd_();
  });
  google.maps.event.addListener(map, 'zoom_changed', function () {
    me.onMapMoveEnd_();
  });
  /**
   * This closure provide easy access to the map.
   * They are used as callbacks, not as methods.
   * @param GMarker marker Marker to be removed from the map
   * @private
   */
  me.removeOverlay_ = function (marker) {
    marker.setMap(null);
    me.shownMarkers_--;
  };
  /**
   * This closure provide easy access to the map.
   * They are used as callbacks, not as methods.
   * @param GMarker marker Marker to be added to the map
   * @private
   */
  me.addOverlay_ = function (marker) {
    if (me.show_) {
      marker.setMap(me.map_);
      me.shownMarkers_++;
    }
  };
  me.resetManager_();
  me.shownMarkers_ = 0;
  me.shownBounds_ = me.getMapGridBounds_();
  
  google.maps.event.trigger(me, 'loaded');
  
};
/**
 *  Default tile size used for deviding the map into a grid.
 */
MarkerManager.DEFAULT_TILE_SIZE_ = 1024;
/*
 *  How much extra space to show around the map border so
 *  dragging doesn't result in an empty place.
 */
MarkerManager.DEFAULT_BORDER_PADDING_ = 100;
/**
 *  Default tilesize of single tile world.
 */
MarkerManager.MERCATOR_ZOOM_LEVEL_ZERO_RANGE = 256;
/**
 * Initializes MarkerManager arrays for all zoom levels
 * Called by constructor and by clearAllMarkers
 */
MarkerManager.prototype.resetManager_ = function () {
  var mapWidth = MarkerManager.MERCATOR_ZOOM_LEVEL_ZERO_RANGE;
  for (var zoom = 0; zoom <= this.maxZoom_; ++zoom) {
    this.grid_[zoom] = {};
    this.numMarkers_[zoom] = 0;
    this.gridWidth_[zoom] = Math.ceil(mapWidth / this.tileSize_);
    mapWidth <<= 1;
  }
};
/**
 * Removes all markers in the manager, and
 * removes any visible markers from the map.
 */
MarkerManager.prototype.clearMarkers = function () {
  this.processAll_(this.shownBounds_, this.removeOverlay_);
  this.resetManager_();
};
/**
 * Gets the tile coordinate for a given latlng point.
 *
 * @param {LatLng} latlng The geographical point.
 * @param {Number} zoom The zoom level.
 * @param {google.maps.Size} padding The padding used to shift the pixel coordinate.
 *               Used for expanding a bounds to include an extra padding
 *               of pixels surrounding the bounds.
 * @return {GPoint} The point in tile coordinates.
 *
 */
MarkerManager.prototype.getTilePoint_ = function (latlng, zoom, padding) {
  var pixelPoint = this.projectionHelper_.LatLngToPixel(latlng, zoom);
  var point = new google.maps.Point(
    Math.floor((pixelPoint.x + padding.width) / this.tileSize_),
    Math.floor((pixelPoint.y + padding.height) / this.tileSize_)
  );
  return point;
};
/**
 * Finds the appropriate place to add the marker to the grid.
 * Optimized for speed; does not actually add the marker to the map.
 * Designed for batch-processing thousands of markers.
 *
 * @param {Marker} marker The marker to add.
 * @param {Number} minZoom The minimum zoom for displaying the marker.
 * @param {Number} maxZoom The maximum zoom for displaying the marker.
 */
MarkerManager.prototype.addMarkerBatch_ = function (marker, minZoom, maxZoom) {
  var me = this;
  var mPoint = marker.getPosition();
  marker.MarkerManager_minZoom = minZoom;
  
  
  // Tracking markers is expensive, so we do this only if the
  // user explicitly requested it when creating marker manager.
  if (this.trackMarkers_) {
    google.maps.event.addListener(marker, 'changed', function (a, b, c) {
      me.onMarkerMoved_(a, b, c);
    });
  }
  var gridPoint = this.getTilePoint_(mPoint, maxZoom, new google.maps.Size(0, 0, 0, 0));
  for (var zoom = maxZoom; zoom >= minZoom; zoom--) {
    var cell = this.getGridCellCreate_(gridPoint.x, gridPoint.y, zoom);
    cell.push(marker);
    gridPoint.x = gridPoint.x >> 1;
    gridPoint.y = gridPoint.y >> 1;
  }
};
/**
 * Returns whether or not the given point is visible in the shown bounds. This
 * is a helper method that takes care of the corner case, when shownBounds have
 * negative minX value.
 *
 * @param {Point} point a point on a grid.
 * @return {Boolean} Whether or not the given point is visible in the currently
 * shown bounds.
 */
MarkerManager.prototype.isGridPointVisible_ = function (point) {
  var vertical = this.shownBounds_.minY <= point.y &&
      point.y <= this.shownBounds_.maxY;
  var minX = this.shownBounds_.minX;
  var horizontal = minX <= point.x && point.x <= this.shownBounds_.maxX;
  if (!horizontal && minX < 0) {
    // Shifts the negative part of the rectangle. As point.x is always less
    // than grid width, only test shifted minX .. 0 part of the shown bounds.
    var width = this.gridWidth_[this.shownBounds_.z];
    horizontal = minX + width <= point.x && point.x <= width - 1;
  }
  return vertical && horizontal;
};
/**
 * Reacts to a notification from a marker that it has moved to a new location.
 * It scans the grid all all zoom levels and moves the marker from the old grid
 * location to a new grid location.
 *
 * @param {Marker} marker The marker that moved.
 * @param {LatLng} oldPoint The old position of the marker.
 * @param {LatLng} newPoint The new position of the marker.
 */
MarkerManager.prototype.onMarkerMoved_ = function (marker, oldPoint, newPoint) {
  // NOTE: We do not know the minimum or maximum zoom the marker was
  // added at, so we start at the absolute maximum. Whenever we successfully
  // remove a marker at a given zoom, we add it at the new grid coordinates.
  var zoom = this.maxZoom_;
  var changed = false;
  var oldGrid = this.getTilePoint_(oldPoint, zoom, new google.maps.Size(0, 0, 0, 0));
  var newGrid = this.getTilePoint_(newPoint, zoom, new google.maps.Size(0, 0, 0, 0));
  while (zoom >= 0 && (oldGrid.x !== newGrid.x || oldGrid.y !== newGrid.y)) {
    var cell = this.getGridCellNoCreate_(oldGrid.x, oldGrid.y, zoom);
    if (cell) {
      if (this.removeFromArray_(cell, marker)) {
        this.getGridCellCreate_(newGrid.x, newGrid.y, zoom).push(marker);
      }
    }
    // For the current zoom we also need to update the map. Markers that no
    // longer are visible are removed from the map. Markers that moved into
    // the shown bounds are added to the map. This also lets us keep the count
    // of visible markers up to date.
    if (zoom === this.mapZoom_) {
      if (this.isGridPointVisible_(oldGrid)) {
        if (!this.isGridPointVisible_(newGrid)) {
          this.removeOverlay_(marker);
          changed = true;
        }
      } else {
        if (this.isGridPointVisible_(newGrid)) {
          this.addOverlay_(marker);
          changed = true;
        }
      }
    }
    oldGrid.x = oldGrid.x >> 1;
    oldGrid.y = oldGrid.y >> 1;
    newGrid.x = newGrid.x >> 1;
    newGrid.y = newGrid.y >> 1;
    --zoom;
  }
  if (changed) {
    this.notifyListeners_();
  }
};
/**
 * Removes marker from the manager and from the map
 * (if it's currently visible).
 * @param {GMarker} marker The marker to delete.
 */
MarkerManager.prototype.removeMarker = function (marker) {
  var zoom = this.maxZoom_;
  var changed = false;
  var point = marker.getPosition();
  var grid = this.getTilePoint_(point, zoom, new google.maps.Size(0, 0, 0, 0));
  while (zoom >= 0) {
    var cell = this.getGridCellNoCreate_(grid.x, grid.y, zoom);
    if (cell) {
      this.removeFromArray_(cell, marker);
    }
    // For the current zoom we also need to update the map. Markers that no
    // longer are visible are removed from the map. This also lets us keep the count
    // of visible markers up to date.
    if (zoom === this.mapZoom_) {
      if (this.isGridPointVisible_(grid)) {
        this.removeOverlay_(marker);
        changed = true;
      }
    }
    grid.x = grid.x >> 1;
    grid.y = grid.y >> 1;
    --zoom;
  }
  if (changed) {
    this.notifyListeners_();
  }
  this.numMarkers_[marker.MarkerManager_minZoom]--;
};
/**
 * Add many markers at once.
 * Does not actually update the map, just the internal grid.
 *
 * @param {Array of Marker} markers The markers to add.
 * @param {Number} minZoom The minimum zoom level to display the markers.
 * @param {Number} opt_maxZoom The maximum zoom level to display the markers.
 */
MarkerManager.prototype.addMarkers = function (markers, minZoom, opt_maxZoom) {
  var maxZoom = this.getOptMaxZoom_(opt_maxZoom);
  for (var i = markers.length - 1; i >= 0; i--) {
    this.addMarkerBatch_(markers[i], minZoom, maxZoom);
  }
  this.numMarkers_[minZoom] += markers.length;
};
/**
 * Returns the value of the optional maximum zoom. This method is defined so
 * that we have just one place where optional maximum zoom is calculated.
 *
 * @param {Number} opt_maxZoom The optinal maximum zoom.
 * @return The maximum zoom.
 */
MarkerManager.prototype.getOptMaxZoom_ = function (opt_maxZoom) {
  return opt_maxZoom || this.maxZoom_;
};
/**
 * Calculates the total number of markers potentially visible at a given
 * zoom level.
 *
 * @param {Number} zoom The zoom level to check.
 */
MarkerManager.prototype.getMarkerCount = function (zoom) {
  var total = 0;
  for (var z = 0; z <= zoom; z++) {
    total += this.numMarkers_[z];
  }
  return total;
};
/** 
 * Returns a marker given latitude, longitude and zoom. If the marker does not 
 * exist, the method will return a new marker. If a new marker is created, 
 * it will NOT be added to the manager. 
 * 
 * @param {Number} lat - the latitude of a marker. 
 * @param {Number} lng - the longitude of a marker. 
 * @param {Number} zoom - the zoom level 
 * @return {GMarker} marker - the marker found at lat and lng 
 */ 
MarkerManager.prototype.getMarker = function (lat, lng, zoom) {
  var mPoint = new google.maps.LatLng(lat, lng); 
  var gridPoint = this.getTilePoint_(mPoint, zoom, new google.maps.Size(0, 0, 0, 0));
  var marker = new google.maps.Marker({position: mPoint}); 
    
  var cellArray = this.getGridCellNoCreate_(gridPoint.x, gridPoint.y, zoom);
  if (cellArray !== undefined) {
    for (var i = 0; i < cellArray.length; i++) 
    { 
      if (lat === cellArray[i].getLatLng().lat() && lng === cellArray[i].getLatLng().lng()) {
        marker = cellArray[i]; 
      } 
    } 
  } 
  return marker; 
}; 
/**
 * Add a single marker to the map.
 *
 * @param {Marker} marker The marker to add.
 * @param {Number} minZoom The minimum zoom level to display the marker.
 * @param {Number} opt_maxZoom The maximum zoom level to display the marker.
 */
MarkerManager.prototype.addMarker = function (marker, minZoom, opt_maxZoom) {
  var maxZoom = this.getOptMaxZoom_(opt_maxZoom);
  this.addMarkerBatch_(marker, minZoom, maxZoom);
  var gridPoint = this.getTilePoint_(marker.getPosition(), this.mapZoom_, new google.maps.Size(0, 0, 0, 0));
  if (this.isGridPointVisible_(gridPoint) &&
      minZoom <= this.shownBounds_.z &&
      this.shownBounds_.z <= maxZoom) {
    this.addOverlay_(marker);
    this.notifyListeners_();
  }
  this.numMarkers_[minZoom]++;
};
/**
 * Helper class to create a bounds of INT ranges.
 * @param bounds Array.<Object.<string, number>> Bounds object.
 * @constructor
 */
function GridBounds(bounds) {
  // [sw, ne]
  
  this.minX = Math.min(bounds[0].x, bounds[1].x);
  this.maxX = Math.max(bounds[0].x, bounds[1].x);
  this.minY = Math.min(bounds[0].y, bounds[1].y);
  this.maxY = Math.max(bounds[0].y, bounds[1].y);
      
}
/**
 * Returns true if this bounds equal the given bounds.
 * @param {GridBounds} gridBounds GridBounds The bounds to test.
 * @return {Boolean} This Bounds equals the given GridBounds.
 */
GridBounds.prototype.equals = function (gridBounds) {
  if (this.maxX === gridBounds.maxX && this.maxY === gridBounds.maxY && this.minX === gridBounds.minX && this.minY === gridBounds.minY) {
    return true;
  } else {
    return false;
  }  
};
/**
 * Returns true if this bounds (inclusively) contains the given point.
 * @param {Point} point  The point to test.
 * @return {Boolean} This Bounds contains the given Point.
 */
GridBounds.prototype.containsPoint = function (point) {
  var outer = this;
  return (outer.minX <= point.x && outer.maxX >= point.x && outer.minY <= point.y && outer.maxY >= point.y);
};
/**
 * Get a cell in the grid, creating it first if necessary.
 *
 * Optimization candidate
 *
 * @param {Number} x The x coordinate of the cell.
 * @param {Number} y The y coordinate of the cell.
 * @param {Number} z The z coordinate of the cell.
 * @return {Array} The cell in the array.
 */
MarkerManager.prototype.getGridCellCreate_ = function (x, y, z) {
  var grid = this.grid_[z];
  if (x < 0) {
    x += this.gridWidth_[z];
  }
  var gridCol = grid[x];
  if (!gridCol) {
    gridCol = grid[x] = [];
    return (gridCol[y] = []);
  }
  var gridCell = gridCol[y];
  if (!gridCell) {
    return (gridCol[y] = []);
  }
  return gridCell;
};
/**
 * Get a cell in the grid, returning undefined if it does not exist.
 *
 * NOTE: Optimized for speed -- otherwise could combine with getGridCellCreate_.
 *
 * @param {Number} x The x coordinate of the cell.
 * @param {Number} y The y coordinate of the cell.
 * @param {Number} z The z coordinate of the cell.
 * @return {Array} The cell in the array.
 */
MarkerManager.prototype.getGridCellNoCreate_ = function (x, y, z) {
  var grid = this.grid_[z];
  
  if (x < 0) {
    x += this.gridWidth_[z];
  }
  var gridCol = grid[x];
  return gridCol ? gridCol[y] : undefined;
};
/**
 * Turns at geographical bounds into a grid-space bounds.
 *
 * @param {LatLngBounds} bounds The geographical bounds.
 * @param {Number} zoom The zoom level of the bounds.
 * @param {google.maps.Size} swPadding The padding in pixels to extend beyond the
 * given bounds.
 * @param {google.maps.Size} nePadding The padding in pixels to extend beyond the
 * given bounds.
 * @return {GridBounds} The bounds in grid space.
 */
MarkerManager.prototype.getGridBounds_ = function (bounds, zoom, swPadding, nePadding) {
  zoom = Math.min(zoom, this.maxZoom_);
  var bl = bounds.getSouthWest();
  var tr = bounds.getNorthEast();
  var sw = this.getTilePoint_(bl, zoom, swPadding);
  var ne = this.getTilePoint_(tr, zoom, nePadding);
  var gw = this.gridWidth_[zoom];
  // Crossing the prime meridian requires correction of bounds.
  if (tr.lng() < bl.lng() || ne.x < sw.x) {
    sw.x -= gw;
  }
  if (ne.x - sw.x  + 1 >= gw) {
    // Computed grid bounds are larger than the world; truncate.
    sw.x = 0;
    ne.x = gw - 1;
  }
  var gridBounds = new GridBounds([sw, ne]);
  gridBounds.z = zoom;
  return gridBounds;
};
/**
 * Gets the grid-space bounds for the current map viewport.
 *
 * @return {Bounds} The bounds in grid space.
 */
MarkerManager.prototype.getMapGridBounds_ = function () {
  return this.getGridBounds_(this.map_.getBounds(), this.mapZoom_, this.swPadding_, this.nePadding_);
};
/**
 * Event listener for map:movend.
 * NOTE: Use a timeout so that the user is not blocked
 * from moving the map.
 *
 * Removed this because a a lack of a scopy override/callback function on events. 
 */
MarkerManager.prototype.onMapMoveEnd_ = function () {
  this.objectSetTimeout_(this, this.updateMarkers_, 0);
};
/**
 * Call a function or evaluate an expression after a specified number of
 * milliseconds.
 *
 * Equivalent to the standard window.setTimeout function, but the given
 * function executes as a method of this instance. So the function passed to
 * objectSetTimeout can contain references to this.
 *    objectSetTimeout(this, function () { alert(this.x) }, 1000);
 *
 * @param {Object} object  The target object.
 * @param {Function} command  The command to run.
 * @param {Number} milliseconds  The delay.
 * @return {Boolean}  Success.
 */
MarkerManager.prototype.objectSetTimeout_ = function (object, command, milliseconds) {
  return window.setTimeout(function () {
    command.call(object);
  }, milliseconds);
};
/**
 * Is this layer visible?
 *
 * Returns visibility setting
 *
 * @return {Boolean} Visible
 */
MarkerManager.prototype.visible = function () {
  return this.show_ ? true : false;
};
/**
 * Returns true if the manager is hidden.
 * Otherwise returns false.
 * @return {Boolean} Hidden
 */
MarkerManager.prototype.isHidden = function () {
  return !this.show_;
};
/**
 * Shows the manager if it's currently hidden.
 */
MarkerManager.prototype.show = function () {
  this.show_ = true;
  this.refresh();
};
/**
 * Hides the manager if it's currently visible
 */
MarkerManager.prototype.hide = function () {
  this.show_ = false;
  this.refresh();
};
/**
 * Toggles the visibility of the manager.
 */
MarkerManager.prototype.toggle = function () {
  this.show_ = !this.show_;
  this.refresh();
};
/**
 * Refresh forces the marker-manager into a good state.
 * <ol>
 *   <li>If never before initialized, shows all the markers.</li>
 *   <li>If previously initialized, removes and re-adds all markers.</li>
 * </ol>
 */
MarkerManager.prototype.refresh = function () {
  if (this.shownMarkers_ > 0) {
    this.processAll_(this.shownBounds_, this.removeOverlay_);
  }
  // An extra check on this.show_ to increase performance (no need to processAll_)
  if (this.show_) {
    this.processAll_(this.shownBounds_, this.addOverlay_);
  }
  this.notifyListeners_();
};
/**
 * After the viewport may have changed, add or remove markers as needed.
 */
MarkerManager.prototype.updateMarkers_ = function () {
  this.mapZoom_ = this.map_.getZoom();
  var newBounds = this.getMapGridBounds_();
    
  // If the move does not include new grid sections,
  // we have no work to do:
  if (newBounds.equals(this.shownBounds_) && newBounds.z === this.shownBounds_.z) {
    return;
  }
  if (newBounds.z !== this.shownBounds_.z) {
    this.processAll_(this.shownBounds_, this.removeOverlay_);
    if (this.show_) { // performance
      this.processAll_(newBounds, this.addOverlay_);
    }
  } else {
    // Remove markers:
    this.rectangleDiff_(this.shownBounds_, newBounds, this.removeCellMarkers_);
    // Add markers:
    if (this.show_) { // performance
      this.rectangleDiff_(newBounds, this.shownBounds_, this.addCellMarkers_);
    }
  }
  this.shownBounds_ = newBounds;
  this.notifyListeners_();
};
/**
 * Notify listeners when the state of what is displayed changes.
 */
MarkerManager.prototype.notifyListeners_ = function () {
  google.maps.event.trigger(this, 'changed', this.shownBounds_, this.shownMarkers_);
};
/**
 * Process all markers in the bounds provided, using a callback.
 *
 * @param {Bounds} bounds The bounds in grid space.
 * @param {Function} callback The function to call for each marker.
 */
MarkerManager.prototype.processAll_ = function (bounds, callback) {
  for (var x = bounds.minX; x <= bounds.maxX; x++) {
    for (var y = bounds.minY; y <= bounds.maxY; y++) {
      this.processCellMarkers_(x, y,  bounds.z, callback);
    }
  }
};
/**
 * Process all markers in the grid cell, using a callback.
 *
 * @param {Number} x The x coordinate of the cell.
 * @param {Number} y The y coordinate of the cell.
 * @param {Number} z The z coordinate of the cell.
 * @param {Function} callback The function to call for each marker.
 */
MarkerManager.prototype.processCellMarkers_ = function (x, y, z, callback) {
  var cell = this.getGridCellNoCreate_(x, y, z);
  if (cell) {
    for (var i = cell.length - 1; i >= 0; i--) {
      callback(cell[i]);
    }
  }
};
/**
 * Remove all markers in a grid cell.
 *
 * @param {Number} x The x coordinate of the cell.
 * @param {Number} y The y coordinate of the cell.
 * @param {Number} z The z coordinate of the cell.
 */
MarkerManager.prototype.removeCellMarkers_ = function (x, y, z) {
  this.processCellMarkers_(x, y, z, this.removeOverlay_);
};
/**
 * Add all markers in a grid cell.
 *
 * @param {Number} x The x coordinate of the cell.
 * @param {Number} y The y coordinate of the cell.
 * @param {Number} z The z coordinate of the cell.
 */
MarkerManager.prototype.addCellMarkers_ = function (x, y, z) {
  this.processCellMarkers_(x, y, z, this.addOverlay_);
};
/**
 * Use the rectangleDiffCoords_ function to process all grid cells
 * that are in bounds1 but not bounds2, using a callback, and using
 * the current MarkerManager object as the instance.
 *
 * Pass the z parameter to the callback in addition to x and y.
 *
 * @param {Bounds} bounds1 The bounds of all points we may process.
 * @param {Bounds} bounds2 The bounds of points to exclude.
 * @param {Function} callback The callback function to call
 *                   for each grid coordinate (x, y, z).
 */
MarkerManager.prototype.rectangleDiff_ = function (bounds1, bounds2, callback) {
  var me = this;
  me.rectangleDiffCoords_(bounds1, bounds2, function (x, y) {
    callback.apply(me, [x, y, bounds1.z]);
  });
};
/**
 * Calls the function for all points in bounds1, not in bounds2
 *
 * @param {Bounds} bounds1 The bounds of all points we may process.
 * @param {Bounds} bounds2 The bounds of points to exclude.
 * @param {Function} callback The callback function to call
 *                   for each grid coordinate.
 */
MarkerManager.prototype.rectangleDiffCoords_ = function (bounds1, bounds2, callback) {
  var minX1 = bounds1.minX;
  var minY1 = bounds1.minY;
  var maxX1 = bounds1.maxX;
  var maxY1 = bounds1.maxY;
  var minX2 = bounds2.minX;
  var minY2 = bounds2.minY;
  var maxX2 = bounds2.maxX;
  var maxY2 = bounds2.maxY;
  var x, y;
  for (x = minX1; x <= maxX1; x++) {  // All x in R1
    // All above:
    for (y = minY1; y <= maxY1 && y < minY2; y++) {  // y in R1 above R2
      callback(x, y);
    }
    // All below:
    for (y = Math.max(maxY2 + 1, minY1);  // y in R1 below R2
         y <= maxY1; y++) {
      callback(x, y);
    }
  }
  for (y = Math.max(minY1, minY2);
       y <= Math.min(maxY1, maxY2); y++) {  // All y in R2 and in R1
    // Strictly left:
    for (x = Math.min(maxX1 + 1, minX2) - 1;
         x >= minX1; x--) {  // x in R1 left of R2
      callback(x, y);
    }
    // Strictly right:
    for (x = Math.max(minX1, maxX2 + 1);  // x in R1 right of R2
         x <= maxX1; x++) {
      callback(x, y);
    }
  }
};
/**
 * Removes value from array. O(N).
 *
 * @param {Array} array  The array to modify.
 * @param {any} value  The value to remove.
 * @param {Boolean} opt_notype  Flag to disable type checking in equality.
 * @return {Number}  The number of instances of value that were removed.
 */
MarkerManager.prototype.removeFromArray_ = function (array, value, opt_notype) {
  var shift = 0;
  for (var i = 0; i < array.length; ++i) {
    if (array[i] === value || (opt_notype && array[i] === value)) {
      array.splice(i--, 1);
      shift++;
    }
  }
  return shift;
};
/**
*   Projection overlay helper. Helps in calculating
*   that markers get into the right grid.
*   @constructor
*   @param {Map} map The map to manage.
**/
function ProjectionHelperOverlay(map) {
  
  this.setMap(map);
  var TILEFACTOR = 8;
  var TILESIDE = 1 << TILEFACTOR;
  var RADIUS = 7;
  this._map = map;
  this._zoom = -1;
  this._X0 =
  this._Y0 =
  this._X1 =
  this._Y1 = -1;
  
}
ProjectionHelperOverlay.prototype = new google.maps.OverlayView();
/**
 *  Helper function to convert Lng to X
 *  @private
 *  @param {float} lng
 **/
ProjectionHelperOverlay.prototype.LngToX_ = function (lng) {
  return (1 + lng / 180);
};
/**
 *  Helper function to convert Lat to Y
 *  @private
 *  @param {float} lat
 **/
ProjectionHelperOverlay.prototype.LatToY_ = function (lat) {
  var sinofphi = Math.sin(lat * Math.PI / 180);
  return (1 - 0.5 / Math.PI * Math.log((1 + sinofphi) / (1 - sinofphi)));
};
/**
*   Old school LatLngToPixel
*   @param {LatLng} latlng google.maps.LatLng object
*   @param {Number} zoom Zoom level
*   @return {position} {x: pixelPositionX, y: pixelPositionY}
**/
ProjectionHelperOverlay.prototype.LatLngToPixel = function (latlng, zoom) {
  var map = this._map;
  var div = this.getProjection().fromLatLngToDivPixel(latlng);
  var abs = {x: ~~(0.5 + this.LngToX_(latlng.lng()) * (2 << (zoom + 6))), y: ~~(0.5 + this.LatToY_(latlng.lat()) * (2 << (zoom + 6)))};
  return abs;
};
/**
 * Draw function only triggers a ready event for
 * MarkerManager to know projection can proceed to
 * initialize.
 */
ProjectionHelperOverlay.prototype.draw = function () {
  if (!this.ready) {
    this.ready = true;
    google.maps.event.trigger(this, 'ready');
  }
};
// ==ClosureCompiler==
function MarkerClusterer(e,t,n){this.extend(MarkerClusterer,google.maps.OverlayView);this.map_=e;this.markers_=[];this.clusters_=[];this.sizes=[53,56,66,78,90];this.styles_=[];this.ready_=false;var r=n||{};this.gridSize_=r["gridSize"]||60;this.minClusterSize_=r["minimumClusterSize"]||2;this.maxZoom_=r["maxZoom"]||null;this.styles_=r["styles"]||[];this.imagePath_=r["imagePath"]||this.MARKER_CLUSTER_IMAGE_PATH_;this.imageExtension_=r["imageExtension"]||this.MARKER_CLUSTER_IMAGE_EXTENSION_;this.zoomOnClick_=true;if(r["zoomOnClick"]!=undefined){this.zoomOnClick_=r["zoomOnClick"]}this.averageCenter_=false;if(r["averageCenter"]!=undefined){this.averageCenter_=r["averageCenter"]}this.setupStyles_();this.setMap(e);this.prevZoom_=this.map_.getZoom();var i=this;google.maps.event.addListener(this.map_,"zoom_changed",function(){var e=i.map_.getZoom();var t=i.map_.minZoom||0;var n=Math.min(i.map_.maxZoom||100,i.map_.mapTypes[i.map_.getMapTypeId()].maxZoom);e=Math.min(Math.max(e,t),n);if(i.prevZoom_!=e){i.prevZoom_=e;i.resetViewport()}});google.maps.event.addListener(this.map_,"idle",function(){i.redraw()});if(t&&t.length){this.addMarkers(t,false)}}function Cluster(e){this.markerClusterer_=e;this.map_=e.getMap();this.gridSize_=e.getGridSize();this.minClusterSize_=e.getMinClusterSize();this.averageCenter_=e.isAverageCenter();this.center_=null;this.markers_=[];this.bounds_=null;this.clusterIcon_=new ClusterIcon(this,e.getStyles(),e.getGridSize())}function ClusterIcon(e,t,n){e.getMarkerClusterer().extend(ClusterIcon,google.maps.OverlayView);this.styles_=t;this.padding_=n||0;this.cluster_=e;this.center_=null;this.map_=e.getMap();this.div_=null;this.sums_=null;this.visible_=false;this.setMap(this.map_)}MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_PATH_="https://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/"+"images/m";MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_EXTENSION_="png";MarkerClusterer.prototype.extend=function(e,t){return function(e){for(var t in e.prototype){this.prototype[t]=e.prototype[t]}return this}.apply(e,[t])};MarkerClusterer.prototype.onAdd=function(){this.setReady_(true)};MarkerClusterer.prototype.draw=function(){};MarkerClusterer.prototype.setupStyles_=function(){if(this.styles_.length){return}for(var e=0,t;t=this.sizes[e];e++){this.styles_.push({url:this.imagePath_+(e+1)+"."+this.imageExtension_,height:t,width:t})}};MarkerClusterer.prototype.fitMapToMarkers=function(){var e=this.getMarkers();var t=new google.maps.LatLngBounds;for(var n=0,r;r=e[n];n++){t.extend(r.getPosition())}this.map_.fitBounds(t)};MarkerClusterer.prototype.setStyles=function(e){this.styles_=e};MarkerClusterer.prototype.getStyles=function(){return this.styles_};MarkerClusterer.prototype.isZoomOnClick=function(){return this.zoomOnClick_};MarkerClusterer.prototype.isAverageCenter=function(){return this.averageCenter_};MarkerClusterer.prototype.getMarkers=function(){return this.markers_};MarkerClusterer.prototype.getTotalMarkers=function(){return this.markers_.length};MarkerClusterer.prototype.setMaxZoom=function(e){this.maxZoom_=e};MarkerClusterer.prototype.getMaxZoom=function(){return this.maxZoom_};MarkerClusterer.prototype.calculator_=function(e,t){var n=0;var r=e.length;var i=r;while(i!==0){i=parseInt(i/10,10);n++}n=Math.min(n,t);return{text:r,index:n}};MarkerClusterer.prototype.setCalculator=function(e){this.calculator_=e};MarkerClusterer.prototype.getCalculator=function(){return this.calculator_};MarkerClusterer.prototype.addMarkers=function(e,t){for(var n=0,r;r=e[n];n++){this.pushMarkerTo_(r)}if(!t){this.redraw()}};MarkerClusterer.prototype.pushMarkerTo_=function(e){e.isAdded=false;if(e["draggable"]){var t=this;google.maps.event.addListener(e,"dragend",function(){e.isAdded=false;t.repaint()})}this.markers_.push(e)};MarkerClusterer.prototype.addMarker=function(e,t){this.pushMarkerTo_(e);if(!t){this.redraw()}};MarkerClusterer.prototype.removeMarker_=function(e){var t=-1;if(this.markers_.indexOf){t=this.markers_.indexOf(e)}else{for(var n=0,r;r=this.markers_[n];n++){if(r==e){t=n;break}}}if(t==-1){return false}e.setMap(null);this.markers_.splice(t,1);return true};MarkerClusterer.prototype.removeMarker=function(e,t){var n=this.removeMarker_(e);if(!t&&n){this.resetViewport();this.redraw();return true}else{return false}};MarkerClusterer.prototype.removeMarkers=function(e,t){var n=false;for(var r=0,i;i=e[r];r++){var s=this.removeMarker_(i);n=n||s}if(!t&&n){this.resetViewport();this.redraw();return true}};MarkerClusterer.prototype.setReady_=function(e){if(!this.ready_){this.ready_=e;this.createClusters_()}};MarkerClusterer.prototype.getTotalClusters=function(){return this.clusters_.length};MarkerClusterer.prototype.getMap=function(){return this.map_};MarkerClusterer.prototype.setMap=function(e){this.map_=e};MarkerClusterer.prototype.getGridSize=function(){return this.gridSize_};MarkerClusterer.prototype.setGridSize=function(e){this.gridSize_=e};MarkerClusterer.prototype.getMinClusterSize=function(){return this.minClusterSize_};MarkerClusterer.prototype.setMinClusterSize=function(e){this.minClusterSize_=e};MarkerClusterer.prototype.getExtendedBounds=function(e){var t=this.getProjection();var n=new google.maps.LatLng(e.getNorthEast().lat(),e.getNorthEast().lng());var r=new google.maps.LatLng(e.getSouthWest().lat(),e.getSouthWest().lng());var i=t.fromLatLngToDivPixel(n);i.x+=this.gridSize_;i.y-=this.gridSize_;var s=t.fromLatLngToDivPixel(r);s.x-=this.gridSize_;s.y+=this.gridSize_;var o=t.fromDivPixelToLatLng(i);var u=t.fromDivPixelToLatLng(s);e.extend(o);e.extend(u);return e};MarkerClusterer.prototype.isMarkerInBounds_=function(e,t){return t.contains(e.getPosition())};MarkerClusterer.prototype.clearMarkers=function(){this.resetViewport(true);this.markers_=[]};MarkerClusterer.prototype.resetViewport=function(e){for(var t=0,n;n=this.clusters_[t];t++){n.remove()}for(var t=0,r;r=this.markers_[t];t++){r.isAdded=false;if(e){r.setMap(null)}}this.clusters_=[]};MarkerClusterer.prototype.repaint=function(){var e=this.clusters_.slice();this.clusters_.length=0;this.resetViewport();this.redraw();window.setTimeout(function(){for(var t=0,n;n=e[t];t++){n.remove()}},0)};MarkerClusterer.prototype.redraw=function(){this.createClusters_()};MarkerClusterer.prototype.distanceBetweenPoints_=function(e,t){if(!e||!t){return 0}var n=6371;var r=(t.lat()-e.lat())*Math.PI/180;var i=(t.lng()-e.lng())*Math.PI/180;var s=Math.sin(r/2)*Math.sin(r/2)+Math.cos(e.lat()*Math.PI/180)*Math.cos(t.lat()*Math.PI/180)*Math.sin(i/2)*Math.sin(i/2);var o=2*Math.atan2(Math.sqrt(s),Math.sqrt(1-s));var u=n*o;return u};MarkerClusterer.prototype.addToClosestCluster_=function(e){var t=4e4;var n=null;var r=e.getPosition();for(var i=0,s;s=this.clusters_[i];i++){var o=s.getCenter();if(o){var u=this.distanceBetweenPoints_(o,e.getPosition());if(u<t){t=u;n=s}}}if(n&&n.isMarkerInClusterBounds(e)){n.addMarker(e)}else{var s=new Cluster(this);s.addMarker(e);this.clusters_.push(s)}};MarkerClusterer.prototype.createClusters_=function(){if(!this.ready_){return}var e=new google.maps.LatLngBounds(this.map_.getBounds().getSouthWest(),this.map_.getBounds().getNorthEast());var t=this.getExtendedBounds(e);for(var n=0,r;r=this.markers_[n];n++){if(!r.isAdded&&this.isMarkerInBounds_(r,t)){this.addToClosestCluster_(r)}}};Cluster.prototype.isMarkerAlreadyAdded=function(e){if(this.markers_.indexOf){return this.markers_.indexOf(e)!=-1}else{for(var t=0,n;n=this.markers_[t];t++){if(n==e){return true}}}return false};Cluster.prototype.addMarker=function(e){if(this.isMarkerAlreadyAdded(e)){return false}if(!this.center_){this.center_=e.getPosition();this.calculateBounds_()}else{if(this.averageCenter_){var t=this.markers_.length+1;var n=(this.center_.lat()*(t-1)+e.getPosition().lat())/t;var r=(this.center_.lng()*(t-1)+e.getPosition().lng())/t;this.center_=new google.maps.LatLng(n,r);this.calculateBounds_()}}e.isAdded=true;this.markers_.push(e);var i=this.markers_.length;if(i<this.minClusterSize_&&e.getMap()!=this.map_){e.setMap(this.map_)}if(i==this.minClusterSize_){for(var s=0;s<i;s++){this.markers_[s].setMap(null)}}if(i>=this.minClusterSize_){e.setMap(null)}this.updateIcon();return true};Cluster.prototype.getMarkerClusterer=function(){return this.markerClusterer_};Cluster.prototype.getBounds=function(){var e=new google.maps.LatLngBounds(this.center_,this.center_);var t=this.getMarkers();for(var n=0,r;r=t[n];n++){e.extend(r.getPosition())}return e};Cluster.prototype.remove=function(){this.clusterIcon_.remove();this.markers_.length=0;delete this.markers_};Cluster.prototype.getSize=function(){return this.markers_.length};Cluster.prototype.getMarkers=function(){return this.markers_};Cluster.prototype.getCenter=function(){return this.center_};Cluster.prototype.calculateBounds_=function(){var e=new google.maps.LatLngBounds(this.center_,this.center_);this.bounds_=this.markerClusterer_.getExtendedBounds(e)};Cluster.prototype.isMarkerInClusterBounds=function(e){return this.bounds_.contains(e.getPosition())};Cluster.prototype.getMap=function(){return this.map_};Cluster.prototype.updateIcon=function(){var e=this.map_.getZoom();var t=this.markerClusterer_.getMaxZoom();if(t&&e>t){for(var n=0,r;r=this.markers_[n];n++){r.setMap(this.map_)}return}if(this.markers_.length<this.minClusterSize_){this.clusterIcon_.hide();return}var i=this.markerClusterer_.getStyles().length;var s=this.markerClusterer_.getCalculator()(this.markers_,i);this.clusterIcon_.setCenter(this.center_);this.clusterIcon_.setSums(s);this.clusterIcon_.show()};ClusterIcon.prototype.triggerClusterClick=function(){var e=this.cluster_.getMarkerClusterer();google.maps.event.trigger(e,"clusterclick",this.cluster_);if(e.isZoomOnClick()){this.map_.fitBounds(this.cluster_.getBounds())}};ClusterIcon.prototype.onAdd=function(){this.div_=document.createElement("DIV");if(this.visible_){var e=this.getPosFromLatLng_(this.center_);this.div_.style.cssText=this.createCss(e);this.div_.innerHTML=this.sums_.text}var t=this.getPanes();t.overlayMouseTarget.appendChild(this.div_);var n=this;google.maps.event.addDomListener(this.div_,"click",function(){n.triggerClusterClick()})};ClusterIcon.prototype.getPosFromLatLng_=function(e){var t=this.getProjection().fromLatLngToDivPixel(e);t.x-=parseInt(this.width_/2,10);t.y-=parseInt(this.height_/2,10);return t};ClusterIcon.prototype.draw=function(){if(this.visible_){var e=this.getPosFromLatLng_(this.center_);this.div_.style.top=e.y+"px";this.div_.style.left=e.x+"px"}};ClusterIcon.prototype.hide=function(){if(this.div_){this.div_.style.display="none"}this.visible_=false};ClusterIcon.prototype.show=function(){if(this.div_){var e=this.getPosFromLatLng_(this.center_);this.div_.style.cssText=this.createCss(e);this.div_.style.display=""}this.visible_=true};ClusterIcon.prototype.remove=function(){this.setMap(null)};ClusterIcon.prototype.onRemove=function(){if(this.div_&&this.div_.parentNode){this.hide();this.div_.parentNode.removeChild(this.div_);this.div_=null}};ClusterIcon.prototype.setSums=function(e){this.sums_=e;this.text_=e.text;this.index_=e.index;if(this.div_){this.div_.innerHTML=e.text}this.useStyle()};ClusterIcon.prototype.useStyle=function(){var e=Math.max(0,this.sums_.index-1);e=Math.min(this.styles_.length-1,e);var t=this.styles_[e];this.url_=t["url"];this.height_=t["height"];this.width_=t["width"];this.textColor_=t["textColor"];this.anchor_=t["anchor"];this.textSize_=t["textSize"];this.backgroundPosition_=t["backgroundPosition"]};ClusterIcon.prototype.setCenter=function(e){this.center_=e};ClusterIcon.prototype.createCss=function(e){var t=[];t.push("background-image:url("+this.url_+");");var n=this.backgroundPosition_?this.backgroundPosition_:"0 0";t.push("background-position:"+n+";");if(typeof this.anchor_==="object"){if(typeof this.anchor_[0]==="number"&&this.anchor_[0]>0&&this.anchor_[0]<this.height_){t.push("height:"+(this.height_-this.anchor_[0])+"px; padding-top:"+this.anchor_[0]+"px;")}else{t.push("height:"+this.height_+"px; line-height:"+this.height_+"px;")}if(typeof this.anchor_[1]==="number"&&this.anchor_[1]>0&&this.anchor_[1]<this.width_){t.push("width:"+(this.width_-this.anchor_[1])+"px; padding-left:"+this.anchor_[1]+"px;")}else{t.push("width:"+this.width_+"px; text-align:center;")}}else{t.push("height:"+this.height_+"px; line-height:"+this.height_+"px; width:"+this.width_+"px; text-align:center;")}var r=this.textColor_?this.textColor_:"black";var i=this.textSize_?this.textSize_:11;t.push("cursor:pointer; top:"+e.y+"px; left:"+e.x+"px; color:"+r+"; position:absolute; font-size:"+i+"px; font-family:Arial,sans-serif; font-weight:bold");return t.join("")};window["MarkerClusterer"]=MarkerClusterer;MarkerClusterer.prototype["addMarker"]=MarkerClusterer.prototype.addMarker;MarkerClusterer.prototype["addMarkers"]=MarkerClusterer.prototype.addMarkers;MarkerClusterer.prototype["clearMarkers"]=MarkerClusterer.prototype.clearMarkers;MarkerClusterer.prototype["fitMapToMarkers"]=MarkerClusterer.prototype.fitMapToMarkers;MarkerClusterer.prototype["getCalculator"]=MarkerClusterer.prototype.getCalculator;MarkerClusterer.prototype["getGridSize"]=MarkerClusterer.prototype.getGridSize;MarkerClusterer.prototype["getExtendedBounds"]=MarkerClusterer.prototype.getExtendedBounds;MarkerClusterer.prototype["getMap"]=MarkerClusterer.prototype.getMap;MarkerClusterer.prototype["getMarkers"]=MarkerClusterer.prototype.getMarkers;MarkerClusterer.prototype["getMaxZoom"]=MarkerClusterer.prototype.getMaxZoom;MarkerClusterer.prototype["getStyles"]=MarkerClusterer.prototype.getStyles;MarkerClusterer.prototype["getTotalClusters"]=MarkerClusterer.prototype.getTotalClusters;MarkerClusterer.prototype["getTotalMarkers"]=MarkerClusterer.prototype.getTotalMarkers;MarkerClusterer.prototype["redraw"]=MarkerClusterer.prototype.redraw;MarkerClusterer.prototype["removeMarker"]=MarkerClusterer.prototype.removeMarker;MarkerClusterer.prototype["removeMarkers"]=MarkerClusterer.prototype.removeMarkers;MarkerClusterer.prototype["resetViewport"]=MarkerClusterer.prototype.resetViewport;MarkerClusterer.prototype["repaint"]=MarkerClusterer.prototype.repaint;MarkerClusterer.prototype["setCalculator"]=MarkerClusterer.prototype.setCalculator;MarkerClusterer.prototype["setGridSize"]=MarkerClusterer.prototype.setGridSize;MarkerClusterer.prototype["setMaxZoom"]=MarkerClusterer.prototype.setMaxZoom;MarkerClusterer.prototype["onAdd"]=MarkerClusterer.prototype.onAdd;MarkerClusterer.prototype["draw"]=MarkerClusterer.prototype.draw;Cluster.prototype["getCenter"]=Cluster.prototype.getCenter;Cluster.prototype["getSize"]=Cluster.prototype.getSize;Cluster.prototype["getMarkers"]=Cluster.prototype.getMarkers;ClusterIcon.prototype["onAdd"]=ClusterIcon.prototype.onAdd;ClusterIcon.prototype["draw"]=ClusterIcon.prototype.draw;ClusterIcon.prototype["onRemove"]=ClusterIcon.prototype.onRemove

// marker infobubble window
function InfoBubble(e){ this.extend(InfoBubble,google.maps.OverlayView);this.baseZIndex_=100;this.isOpen_=false;var t=e||{};if(t["backgroundColor"]==undefined){t["backgroundColor"]=this.BACKGROUND_COLOR_}if(t["borderColor"]==undefined){t["borderColor"]=this.BORDER_COLOR_}if(t["borderRadius"]==undefined){t["borderRadius"]=this.BORDER_RADIUS_}if(t["borderWidth"]==undefined){t["borderWidth"]=this.BORDER_WIDTH_}if(t["padding"]==undefined){t["padding"]=this.PADDING_}if(t["arrowPosition"]==undefined){t["arrowPosition"]=this.ARROW_POSITION_}if(t["minWidth"]==undefined){t["minWidth"]=this.MIN_WIDTH_}this.buildDom_();this.setValues(t)}window["InfoBubble"]=InfoBubble;InfoBubble.prototype.ARROW_SIZE_=15;InfoBubble.prototype.ARROW_STYLE_=0;InfoBubble.prototype.SHADOW_STYLE_=1;InfoBubble.prototype.MIN_WIDTH_=50;InfoBubble.prototype.ARROW_POSITION_=50;InfoBubble.prototype.PADDING_=10;InfoBubble.prototype.BORDER_WIDTH_=1;InfoBubble.prototype.BORDER_COLOR_="#ccc";InfoBubble.prototype.BORDER_RADIUS_=10;InfoBubble.prototype.BACKGROUND_COLOR_="#fff";InfoBubble.prototype.extend=function(e,t){return function(e){for(var t in e.prototype){this.prototype[t]=e.prototype[t]}return this}.apply(e,[t])};InfoBubble.prototype.buildDom_=function(){var e=this.bubble_=document.createElement("DIV");e.style["position"]="absolute";e.style["zIndex"]=this.baseZIndex_;var t=this.close_=document.createElement("IMG");t.style["position"]="absolute";t.style["width"]=this.px(12);t.style["height"]=this.px(12);t.style["border"]=0;t.style["zIndex"]=this.baseZIndex_+1;t.style["cursor"]="pointer";t.src="https://maps.gstatic.com/intl/en_us/mapfiles/close.gif";var n=this;google.maps.event.addDomListener(t,"click",function(){n.close();google.maps.event.trigger(n,"closeclick")});var r=this.contentContainer_=document.createElement("DIV");r.style["overflowX"]="visible";r.style["overflowY"]="visible";r.style["cursor"]="default";r.style["clear"]="both";r.style["position"]="relative";r.className="map_infobubble map_popup";var i=this.content_=document.createElement("DIV");r.appendChild(i);var s=this.arrow_=document.createElement("DIV");s.style["position"]="relative";s.className="map_infoarrow";var o=this.arrowOuter_=document.createElement("DIV");var u=this.arrowInner_=document.createElement("DIV");var a=this.getArrowSize_();o.style["position"]=u.style["position"]="absolute";o.style["left"]=u.style["left"]="50%";o.style["height"]=u.style["height"]="0";o.style["width"]=u.style["width"]="0";o.style["marginLeft"]=this.px(-a);o.style["borderWidth"]=this.px(a);o.style["borderBottomWidth"]=0;var f=document.createElement("DIV");f.style["position"]="absolute";e.style["display"]=f.style["display"]="none";e.appendChild(t);e.appendChild(r);s.appendChild(o);s.appendChild(u);e.appendChild(s);var l=document.createElement("style");l.setAttribute("type","text/css");var c="";l.textContent=c;document.getElementsByTagName("head")[0].appendChild(l)};InfoBubble.prototype.setBackgroundClassName=function(e){this.set("backgroundClassName",e)};InfoBubble.prototype["setBackgroundClassName"]=InfoBubble.prototype.setBackgroundClassName;InfoBubble.prototype.getArrowStyle_=function(){return parseInt(this.get("arrowStyle"),10)||0};InfoBubble.prototype.setArrowStyle=function(e){this.set("arrowStyle",e)};InfoBubble.prototype["setArrowStyle"]=InfoBubble.prototype.setArrowStyle;InfoBubble.prototype.getArrowSize_=function(){return parseInt(this.get("arrowSize"),10)||0};InfoBubble.prototype.getArrowPosition_=function(){return parseInt(this.get("arrowPosition"),10)||0};InfoBubble.prototype.setZIndex=function(e){this.set("zIndex",e)};InfoBubble.prototype["setZIndex"]=InfoBubble.prototype.setZIndex;InfoBubble.prototype.getZIndex=function(){return parseInt(this.get("zIndex"),10)||this.baseZIndex_};InfoBubble.prototype.setShadowStyle=function(e){this.set("shadowStyle",e)};InfoBubble.prototype["setShadowStyle"]=InfoBubble.prototype.setShadowStyle;InfoBubble.prototype.getShadowStyle_=function(){return parseInt(this.get("shadowStyle"),10)||0};InfoBubble.prototype.showCloseButton=function(){this.set("hideCloseButton",false)};InfoBubble.prototype["showCloseButton"]=InfoBubble.prototype.showCloseButton;InfoBubble.prototype.hideCloseButton=function(){this.set("hideCloseButton",true)};InfoBubble.prototype["hideCloseButton"]=InfoBubble.prototype.hideCloseButton;InfoBubble.prototype.getBorderRadius_=function(){return parseInt(this.get("borderRadius"),10)||0};InfoBubble.prototype.getBorderWidth_=function(){return parseInt(this.get("borderWidth"),10)||0};InfoBubble.prototype.setBorderWidth=function(e){this.set("borderWidth",e)};InfoBubble.prototype["setBorderWidth"]=InfoBubble.prototype.setBorderWidth;InfoBubble.prototype.getPadding_=function(){return parseInt(this.get("padding"),10)||0};InfoBubble.prototype.px=function(e){if(e){return e+"px"}return e};InfoBubble.prototype.addEvents_=function(){var e=["mousedown","mousemove","mouseover","mouseout","mouseup","mousewheel","DOMMouseScroll","touchstart","touchend","touchmove","dblclick","contextmenu","click"];var t=this.bubble_;this.listeners_=[];for(var n=0,r;r=e[n];n++){this.listeners_.push(google.maps.event.addDomListener(t,r,function(e){e.cancelBubble=true;if(e.stopPropagation){e.stopPropagation()}}))}};InfoBubble.prototype.onAdd=function(){if(!this.bubble_){this.buildDom_()}this.addEvents_();var e=this.getPanes();if(e){e.floatPane.appendChild(this.bubble_)}};InfoBubble.prototype["onAdd"]=InfoBubble.prototype.onAdd;InfoBubble.prototype.draw=function(){var e=this.getProjection();if(!e){return}var t=this.get("position");if(!t){this.close();return}var n=0;var r=this.getAnchorHeight_();var i=this.getArrowSize_();var s=this.getArrowPosition_();s=s/100;var o=e.fromLatLngToDivPixel(t);var u=this.contentContainer_.offsetWidth;var a=this.bubble_.offsetHeight;if(!u){return}var f=o.y-(a+i);if(r){f-=r}var l=o.x-u*s;this.bubble_.style["top"]=this.px(f);this.bubble_.style["left"]=this.px(l)};InfoBubble.prototype["draw"]=InfoBubble.prototype.draw;InfoBubble.prototype.onRemove=function(){if(this.bubble_&&this.bubble_.parentNode){this.bubble_.parentNode.removeChild(this.bubble_)}for(var e=0,t;t=this.listeners_[e];e++){google.maps.event.removeListener(t)}};InfoBubble.prototype["onRemove"]=InfoBubble.prototype.onRemove;InfoBubble.prototype.isOpen=function(){return this.isOpen_};InfoBubble.prototype["isOpen"]=InfoBubble.prototype.isOpen;InfoBubble.prototype.close=function(){if(this.bubble_){this.bubble_.style["display"]="none"}this.isOpen_=false};InfoBubble.prototype["close"]=InfoBubble.prototype.close;InfoBubble.prototype.open=function(e,t){var n=this;window.setTimeout(function(){n.open_(e,t)},0)};InfoBubble.prototype.open_=function(e,t){this.updateContent_();if(e){this.setMap(e)}if(t){this.set("anchor",t);this.bindTo("anchorPoint",t);this.bindTo("position",t)}this.bubble_.style["display"]="";this.redraw_();this.isOpen_=true;var n=!this.get("disableAutoPan");if(n){var r=this;window.setTimeout(function(){r.panToView()},200)}};InfoBubble.prototype["open"]=InfoBubble.prototype.open;InfoBubble.prototype.setPosition=function(e){if(e){this.set("position",e)}};InfoBubble.prototype["setPosition"]=InfoBubble.prototype.setPosition;InfoBubble.prototype.getPosition=function(){return this.get("position")};InfoBubble.prototype["getPosition"]=InfoBubble.prototype.getPosition;InfoBubble.prototype.panToView=function(){var e=this.getProjection();if(!e){return}if(!this.bubble_){return}var t=this.getAnchorHeight_();var n=this.bubble_.offsetHeight+t;var r=this.get("map");var i=r.getDiv();var s=i.offsetHeight;var o=this.getPosition();var u=e.fromLatLngToContainerPixel(r.getCenter());var a=e.fromLatLngToContainerPixel(o);var f=u.y-n;var l=s-u.y;var c=f<0;var h=0;if(c){f*=-1;h=(f+l)/2}a.y-=h;o=e.fromContainerPixelToLatLng(a);if(r.getCenter()!=o){r.panTo(o)}};InfoBubble.prototype["panToView"]=InfoBubble.prototype.panToView;InfoBubble.prototype.htmlToDocumentFragment_=function(e){e=e.replace(/^\s*([\S\s]*)\b\s*$/,"$1");var t=document.createElement("DIV");t.innerHTML=e;if(t.childNodes.length==1){return t.removeChild(t.firstChild)}else{var n=document.createDocumentFragment();while(t.firstChild){n.appendChild(t.firstChild)}return n}};InfoBubble.prototype.removeChildren_=function(e){if(!e){return}var t;while(t=e.firstChild){e.removeChild(t)}};InfoBubble.prototype.setContent=function(e){this.set("content",e)};InfoBubble.prototype["setContent"]=InfoBubble.prototype.setContent;InfoBubble.prototype.getContent=function(){return this.get("content")};InfoBubble.prototype["getContent"]=InfoBubble.prototype.getContent;InfoBubble.prototype.updateContent_=function(){if(!this.content_){return}this.removeChildren_(this.content_);var e=this.getContent();if(e){if(typeof e=="string"){e=this.htmlToDocumentFragment_(e)}this.content_.appendChild(e);var t=this;var n=this.content_.getElementsByTagName("IMG");for(var r=0,i;i=n[r];r++){google.maps.event.addDomListener(i,"load",function(){t.imageLoaded_()})}google.maps.event.trigger(this,"domready")}this.redraw_()};InfoBubble.prototype.imageLoaded_=function(){var e=!this.get("disableAutoPan");this.redraw_()};InfoBubble.prototype.setMaxWidth=function(e){this.set("maxWidth",e)};InfoBubble.prototype["setMaxWidth"]=InfoBubble.prototype.setMaxWidth;InfoBubble.prototype.setMaxHeight=function(e){this.set("maxHeight",e)};InfoBubble.prototype["setMaxHeight"]=InfoBubble.prototype.setMaxHeight;InfoBubble.prototype.setMinWidth=function(e){this.set("minWidth",e)};InfoBubble.prototype["setMinWidth"]=InfoBubble.prototype.setMinWidth;InfoBubble.prototype.setMinHeight=function(e){this.set("minHeight",e)};InfoBubble.prototype["setMinHeight"]=InfoBubble.prototype.setMinHeight;InfoBubble.prototype.getElementSize_=function(e,t,n){var r=document.createElement("DIV");r.style["display"]="inline";r.style["position"]="absolute";r.style["visibility"]="hidden";if(typeof e=="string"){r.innerHTML=e}else{r.appendChild(e.cloneNode(true))}document.body.appendChild(r);var i=new google.maps.Size(r.offsetWidth,r.offsetHeight);if(t&&i.width>t){r.style["width"]=this.px(t);i=new google.maps.Size(r.offsetWidth,r.offsetHeight)}if(n&&i.height>n){r.style["height"]=this.px(n);i=new google.maps.Size(r.offsetWidth,r.offsetHeight)}document.body.removeChild(r);delete r;return i};InfoBubble.prototype.redraw_=function(){this.figureOutSize_();this.positionCloseButton_();this.draw()};InfoBubble.prototype.figureOutSize_=function(){var e=this.get("map");if(!e){return}var t=this.getPadding_();var n=this.getBorderWidth_();var r=this.getBorderRadius_();var i=this.getArrowSize_();var s=e.getDiv();var o=i*2;var u=s.offsetWidth-o;var a=s.offsetHeight-o-this.getAnchorHeight_();var f=0;var l=this.get("minWidth")||0;var c=this.get("minHeight")||0;var h=this.get("maxWidth")||0;var p=this.get("maxHeight")||0;h=Math.min(u,h);p=Math.min(a,p);var d=0;var v=this.get("content");if(typeof v=="string"){v=this.htmlToDocumentFragment_(v)}if(v){var m=this.getElementSize_(v,h,p);if(l<m.width){l=m.width}if(c<m.height){c=m.height}}if(h){l=Math.min(l,h)}if(p){c=Math.min(c,p)}l=Math.max(l,d);if(l==d){l=l+2*t}i=i*2;l=Math.max(l,i);if(l>u){l=u}if(c>a){c=a-f}this.contentContainer_.style["width"]=this.px(l)};InfoBubble.prototype.getAnchorHeight_=function(){var e=this.get("anchor");if(e){var t=this.get("anchorPoint");if(t){return-1*t.y}}return 0};InfoBubble.prototype.positionCloseButton_=function(){var e=this.getBorderRadius_();var t=this.getBorderWidth_();var n=2;var r=56;r+=t;n+=t;var i=this.contentContainer_;if(i&&i.clientHeight<i.scrollHeight){n+=15}this.close_.style["right"]=this.px(n);this.close_.style["top"]=this.px(r)}


/* Delete google Map marker */
function googlemaplisting_deleteMarkers() {
	if (markerArray && markerArray.length > 0){
		for (i in markerArray){
			if (!isNaN(i)){
				markerArray[i].setMap(null);
				infoBubble.close();
			}
		}
		markerArray.length = 0;
	}
	mgr.clearMarkers();
	if(clustering !=1){
		markerClusterer.clearMarkers();
	}
}


/* Add google Map marker */
function templ_add_googlemap_markers(markers){
	mgr = new MarkerManager( map );	
	infowindow = new google.maps.InfoWindow();
	if (markers && markers.length > 0) {
		for (var i = 0; i < markers.length; i++) {	
			var details = markers[i];
			var image = new google.maps.MarkerImage(details.icons);
			var myLatLng = new google.maps.LatLng(details.location[0], details.location[1]);
			if(typeof details.load_content != 'undefined'){
				var details_load_content=details.load_content;
			}else{
				var details_load_content='';
			}
			
			
			markers[i] = new google.maps.Marker({ title: details.name, position: myLatLng, icon: image ,content: details.message,post_id:details.pid ,load_content:details_load_content});
			markerArray.push(markers[i]);
			
			attachMessage(markers[i], details.message,details.pid,details.load_content);
			bounds.extend(myLatLng);
			var pinpointElement = document.getElementById( 'pinpoint_'+details.pid );
			if ( pinpointElement ) {
				if(pippoint_effects=='hover'){
					google.maps.event.addDomListener( pinpointElement, 'mouseover', (function( theMarker ) {								
					 return function() {
						google.maps.event.trigger( theMarker, 'click' );
					 };
				  })(markers[i]) );
				}else if(pippoint_effects=='click'){
					
					google.maps.event.addDomListener( pinpointElement, 'click', (function( theMarker ) {
					 return function() {
						google.maps.event.trigger( theMarker, 'click' );
					 };
				  })(markers[i]) );
					
				}// Pinpoint click
				
			}// pinpointElement
			
		}
	}// markers if condition

	google.maps.event.addListener(mgr, 'loaded', function() {
		mgr.addMarkers( markerArray, 0 );
		mgr.refresh();
	});
	
	/* Start New information window on map 	*/
	 infowindow = new InfoBubble({
		 maxWidth:210,minWidth:210,display: "inline-block", overflow: "auto" ,backgroundColor:"#fff"
	  });			
	/* End */
	
	/* Set marker cluster on google map */
	if(clustering !=1){
		markerClusterer = new MarkerClusterer(map, markers,{maxZoom: 0,gridSize: 40,styles: null,infoOnClick: 1,infoOnClickZoom: 18,});
		 google.maps.event.addListener(markerClusterer, 'clusterclick', function(cluster) {
			/* Convert lat/long from cluster object to a usable MVCObject */
			var info = new google.maps.MVCObject;
			info.set('position', cluster.center_);
			/*Get markers*/
			var markers = cluster.getMarkers();
			var content = post_id = "";
			/*Get all the titles*/
			for(var i = 0; i < markers.length; i++) {			
				content += markers[i].content + "\n";
				post_id += markers[i].post_id + ",";
				var load_content=markers[i].load_content;
			}
			if(load_content==1){
				content='<div class="google-map-info"><div class="map-inner-wrapper"><div class="map-item-info"><i class="fa fa-circle-o-notch fa-spin fa-2x"></i></div></div></div>';
			}
			if(map.getZoom()==21){
				infowindow.close();
				infowindow.setContent( content );
				infowindow.open(map, info);
				if(typeof load_content != 'undefined'){
					/*Load marke data */
					jQuery.ajax({
						url:tevolutionajaxUrl,
						data:'action=mapmarker_post_detail&post_id='+post_id,
						success:function(results){
							infowindow.close();
							infowindow.setContent(results);
							infowindow.open(map, info);
						}
					});
				}
			}
		});
	}
}

// but that message is not within the marker's instance data 
function attachMessage(marker, msg, post_id, load_content) {
	google.maps.event.addListener(marker, 'click', function() {
		infoBubble.setContent(msg);
		infoBubble.open(map, marker);
		/*Load content is define then get the marker data using ajax */
		if(typeof load_content != 'undefined'){			
		 	/*Load marke data */
			jQuery.ajax({  
				url:tevolutionajaxUrl,			
				data:'action=mapmarker_post_detail&post_id='+post_id,
				success:function(results){
					//alert('Helloo'+results);
					infoBubble.setContent(results);
					infoBubble.open(map, marker);
				}
			});
		}
	});
}	

/* Refresh_markers */
var search_map_ajax = null;
var data_map = null;
function refresh_markers() {
	/*  Is not search page then return script*/
	/*if(is_search=='' || is_search.length <= 0){		
		//return;	
	}*/
	/* is draging and bounds modified  not set then return script */
	if (!dragging  ||  !bounds_modified) return;
	
	
	bounds_modified = false;
	dragging = false;
	/* Get north east and soth west bounds */
	var ne = new_bounds.getNorthEast();
	var sw = new_bounds.getSouthWest();
	if(query_string!=''){
		url_data = 'sw_lat='+sw.lat()+"&ne_lat="+ne.lat()+"&sw_lng="+sw.lng()+"&ne_lng="+ne.lng()+"&"+query_string;
	}else{
		url_data = 'sw_lat='+sw.lat()+"&ne_lat="+ne.lat()+"&sw_lng="+sw.lng()+"&ne_lng="+ne.lng();
	}
	
	
	var from_data=jQuery(".tmpl_filter_results").serialize();	
	jQuery('.search_result_listing').addClass('loading_results');
	
	var opr=(ajaxUrl.indexOf("?")!='-1')? '&' :'?';
	search_map_ajax =jQuery.ajax({
		url:ajaxUrl+opr+'action=search_map_ajax&'+url_data+'&'+from_data,
		type:'POST',
		async: true,
		data:from_data,
		beforeSend : function(){
			if(search_map_ajax != null){
				search_map_ajax.abort();
			}
        },
		success:function(results){			
			jQuery('.search_result_listing').removeClass('loading_results');
			jQuery('.search_result_listing').html(results);
		}
	});
	
	/* Call Google Map markers data */
	//jQuery('.listing_google_map').addClass('loading_map_markers');
	setTimeout(function(){
	data_map =jQuery.ajax({
		url:ajaxUrl+opr+'action=search_map_ajax&data_map=1&'+url_data+'&'+from_data,
		type:'POST',
		async: true,
		data:from_data,
		dataType: 'json',
		beforeSend : function(){
			if(data_map != null){
				data_map.abort();
			}
        },
		success:function(results){
			//jQuery('.listing_google_map').removeClass('loading_map_markers');
			googlemaplisting_deleteMarkers();
			markers=results.markers;
			templ_add_googlemap_markers(markers);
		},
	});
	}, 200);
}