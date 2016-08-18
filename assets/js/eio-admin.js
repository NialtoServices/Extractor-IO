// Extractor IO - JavaScript - Admin
// Copyright (C) 2015 Nialto Services
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License along
// with this program; if not, write to the Free Software Foundation, Inc.,
// 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

var element = null;
jQuery(document).ready(function() {
  jQuery('.eio-extracted-data-link').click(function() {
    var index = jQuery(this).attr('rel');
    tb_show('', '?TB_inline=true&width=900&height=600&inlineId=eio-extracted-data-' + index);
    return false;
  });
});