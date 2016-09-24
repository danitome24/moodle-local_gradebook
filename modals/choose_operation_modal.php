<?php
/**
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
/**
 * @author Daniel Tome <danieltomefer@gmail.com>
 */
?>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="js-title-step"></h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid hide" data-step="1" data-title="Escolleix la operació a aplicar!">
                    <div class="offset3">
                        <input id="local-gradebook-droppable" type="text" class="input-lg"/>
                    </div>
                    <!-- Here goes the operation buttons -->
                    <div class="row-fluid">
                        <div class="offset3 span5">
                            <p>Arrastra una operació cap al input</p>
                            <table id="borderless" class="table local-gradebook-drag-buttons">
                                <tbody>
                                <tr>
                                    <td>
                                        <button value="Suma" class="local-gradebook-draggable">Suma </button>
                                    </td>
                                    <td>
                                        <button value="Resta" class="local-gradebook-draggable">Resta </button>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <button value="Mitja" class="local-gradebook-draggable">Mitja</button>
                                    </td>
                                    <td>
                                        <button value="Màxim" class="local-gradebook-draggable">Màxim</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row-fluid hide" data-step="2" data-title="This is the second and last step!">
                    <div class="well">As you can see, this is the second and last step!</div>
                </div>
                <div class="row-fluid hide" data-step="3" data-title="This is the second and last step!">
                    <div class="well">As you can see, this is the second and last step!</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="local-grade-advopt-clear btn btn-default js-btn-step pull-left" data-orientation="clear"></button>
                <button type="button" class="btn btn-warning js-btn-step" data-orientation="previous"></button>
                <button type="button" class="btn btn-success js-btn-step" data-orientation="next"></button>
            </div>
        </div>
    </div>
</div>
