<?php

namespace xframe\view;

/**
 * JSONView is the view for outputting json
 */
class JSONView extends View {

    /**
     * Generate the JSON
     * @return string
     */
    public function execute() {
		header("Content-type: application/json");
        return json_encode($this->parameters);
    }

}
