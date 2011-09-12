<?php



class Collection {

	var $elements = array();

	var $counter = 0;

	var $pointer = 0;



	function Collection() {



	}



	function add($element) {

		$this->elements[$this->counter] = $element;

		$this->counter++;

		$this->pointer++;

	}



    function nextitem()

    {

      ++$this->pointer;

    }



    function lastitem()

    {

       return $this->pointer == $this->counter;

    }



	function remove($element) {

		$found = null;

		for ($i = 0; $i < count($this->elements); $i++) {

		 	if ($this->elements[$i] == $element) {

				$found = $i;

			}

		}

		if ($found != null) {

			array_splice($this->elements, $found, 1);

			$this->counter--;

			$this->pointer--;

		}

	}



	function contains($element) {

		for ($i = 0; $i < count($this->elements); $i++) {

		 	if ($this->elements[$i] == $element) {

				return true;

			}

		}

		return false;

	}



	function hasNext() {

		return $this->pointer < $this->counter;

	}



    function atEnd(){

        return $this->pointer == $this->counter;

    }



	function hasPrevious() {

		return $this->pointer > 0;

	}



	function next() {

		return $this->elements[++$this->pointer];

	}



    function current() {

        return $this->elements[$this->pointer];

    }



	function first() {

		$this->pointer = 0;

		return $this->elements[$this->pointer];

	}



	function last() {

		$this->pointer = $this->counter;

		return $this->elements[$this->pointer];

	}



	function previous() {

		return $this->elements[--$this->pointer];

	}



	function count() {

		return count($this->elements);

	}

}



?>