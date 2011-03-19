<?php
/**
 * Data table class
 *
 * Supports: storing any data, generic formatters, pagination, styling (via formatters + table class settings)
 *
 */
class Helper_Datatable {

   /**
    * Columns - array of column configurations.
    *
    * array(
    *    key_in_rows => array(
    *       label => string,
    *       class => '' || CSS class for <TH> element,
    *       sortable => false | true,
    *       formatter => function for formatting cell data with this key
    *       )
    * )
    *
    * @var array
    */
   private $columns;

   /**
    * Configuration - array of general configuration for this datatable
    *
    * array(
    *    paginator => KO3 Paginator class instance
    *    sortable => default value for sortable in columns
    *    default_sort => default column by which data is sorted
    *    default_dir => default direction in which data is sorted
    * )
    *
    * @var array
    */
   private $configuration;


   /**
    * Rows - array consisting of rows of data items.
    * @var array
    */
   private $rows;

   function  __construct($columns, $configuration = array()) {
      $this->columns = $columns;
      $this->configuration = $configuration;
   }

   /**
    * Add a row.
    * @param array $row
    * @param int $index (Optional) row index.
    */
   function add($row, $index = null) {
      if(!is_numeric($index)) {
         $index = count($this->rows)-1;
      }
      $this->rows[$index] = $row;
   }

   /**
    * Set the rows to the given value, replacing any old values.
    * @param array $rows
    */
   function values($rows) {
      $this->rows = $rows;
   }

   /**
    * Get a single row, or all the rows.
    * @param int $index (Optional) row index.
    * @return array
    */
   function get($index = null) {
      if(is_numeric($index)) {
         return $this->rows[$index];
      }
      return $this->rows;
   }

   /**
    * Delete a row.
    * @param int $index Row index.
    */
   function delete($index) {
      if(isset($this->rows[$index])) {
         unset($this->rows[$index]);
      }
   }

   /**
    * Render the datatable.
    *
    * Configuration: defaults to $_REQUEST
    * array(
    *    sort => column key
    *    dir => ASC | DESC
    *    page => int (page number)
    * )
    *
    * @return string
    */
   function render($params = null) {
      // create table
      $result = '<table'.( isset($this->configuration['class']) ? ' class="'.$this->configuration['class'].'"' : '' ).'>';

      if(!$params) {
         $params = $_REQUEST;
      }
      // get row sort info
      $sort = isset($params['sort']) ? $params['sort'] : false;
      if(!$sort && !empty($this->configuration['default_sort'])) {
         $sort = $this->configuration['default_sort'];
      }
      $dir = isset($params['dir']) ? $params['dir'] : false;
      if(!$dir && !empty($this->configuration['default_dir'])) {
         $dir = $this->configuration['default_dir'];
      }
      $page = isset($params['page']) ?  $params['page'] : 1;

      // create heading
      $result .= '<thead><tr>';
      
      foreach($this->columns as $name => $column) {
         if (!empty($column['sortable']) || !empty($this->configuration['sortable']) && !isset($column['sortable']) ) {
            if ( ($name == $sort && $dir == 'DESC') || $name != $sort ) {
               $result .= '<th scope="col">'.
               Html::anchor(
                     URL::site(Request::current()->uri(), true).URL::query(array(
                        'page' => $page,
                        'sort' => $name,
                        'dir' => null,
                        )),
                     (isset($column['label']) ? $column['label'] : $name),
                     ($name == $sort ? array('class' => 'desc') : null)
                     ).'</th>';
            } else {
               $result .= '<th scope="col">'.
               Html::anchor(
                     URL::site(Request::current()->uri(), true).URL::query(array(
                        'page' => $page,
                        'sort' => $name,
                        'dir' => 'DESC',
                        )),
                     (isset($column['label']) ? $column['label'] : $name),
                     ($name == $sort ? array('class' => 'asc') : null)
                     ).'</th>';
            }
         } else {
            $result .= '<th scope="col">'.(isset($column['label']) ? $column['label'] : $name).'</th>';
         }
      }
      $result .= '</tr></thead>';

      // print data
      $result .= '<tbody>';
      // array_merge renumbers the array, this is needed because unset (via deleteRow) will leave gaps in the indices.
      $this->rows = array_merge($this->rows);
      $end = count($this->rows);
      $i = 0;
      for($i = 0; $i < $end; $i++) {
         $result .= '<tr';
         if(isset($this->rows[$i]['_class'])) {
               $result .= ' class="'.$this->rows[$i]['_class'].'"';
         } else {
            if (($i % 2) == 0) {
               $result .= ' class="odd"';
            } else {
               $result .= ' class="even"';
            }
         }
         $result .= '>';
         foreach($this->columns as $column => $settings) {
            $value = '';
            // the value does not have to even exist for formatters to work
            // since they might just use some other columns in the data.
            if(isset($settings['formatter']) && is_callable($settings['formatter'])) {
               $value = call_user_func($settings['formatter'], $this->rows[$i]);
            } else if(isset($this->rows[$i][$column])) {
               $value = $this->rows[$i][$column];
            }
            $result .= '<td>'.$value.'</td>';
         }
         $result .= '</tr>';
      }
      $result .= '</tbody>';
      $result .= '</table>';

      return $result;
   }

}