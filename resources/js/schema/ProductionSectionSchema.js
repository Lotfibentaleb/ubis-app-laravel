export default {
  schema: {
    "$schema": "http://json-schema.org/draft-07/schema#",
    "$id": "https://example.com/object1620143319.json",
    "definitions": {},
    "title": "Root",
    "type": "array",
    "default": [],
    "uniqueItems":true,
    "items":{
      "$id": "#root/items",
      "title": "Items",
      "type": "object",
      "required": [
        "title",
      ],
      "additionalProperties": false,
      "dependencies": {
        "calc": ["calc_var_x", "calc_var_y", "calc_var_z"]
      },
      "properties": {
        "max": {
          "$id": "#root/items/max",
          "title": "max",
          "type": "number",
          "description" : "Maximum allowed value",
          "examples": [
            999.55
          ],
          "default": 0
        },
        "min": {
          "$id": "#root/items/min",
          "title": "min",
          "type": "number",
          "description" : "Minumum allowed value",
          "examples": [
            -999.55
          ],
          "default": 0
        },
        "type": {
          "$id": "#root/items/type",
          "title": "type",
          "type": "string",
          "default": "double",
          "examples": [
            "integer",
            "double",
            "bool",
            "time"
          ],
          "pattern": "^.*$",
          "enum": ["integer", "bool", "time", "double"]
        },
        "unit": {
          "$id": "#root/items/unit",
          "title": "unit",
          "type": "string",
          "default": "",
          "examples": [
            "°C"
          ],
          "pattern": "^.*$"
        },
        "title": {
          "$id": "#root/items/title",
          "title": "title",
          "type": "string",
          "default": "",
          "examples": [
            "heating_temp"
          ],
          "pattern": "^.*$"
        },
        "strict": {
          "$id": "#root/items/strict",
          "title": "strict",
          "type": "boolean",
          "examples": [
            false
          ],
          "default": false
        },
        "verify": {
          "$id": "#root/items/verify",
          "title": "verify",
          "type": "boolean",
          "examples": [
            false
          ],
          "default": true
        },
        "nominal": {
          "$id": "#root/items/nominal",
          "title": "nominal",
          "type": ["number","boolean"],
          "examples": [
            30.5
          ],
          "default": 0
        },
        "datatype": {
          "$id": "#root/items/datatype",
          "title": "datatype",
          "type": "string",
          "examples": [
            "timeseries",
            "json"
          ],
          "default": "timeseries",
          "enum": ["timeseries", "json"]
        },
        "calc": {
          "$id": "#root/items/calc",
          "title": "calc",
          "type": "string",
          "examples": [
            "y-(3.987924586394250*10^-12*(x+1)^6"
          ],
          "default": ""
        },
        "calc_var_x": {
          "$id": "#root/items/calc_var_x",
          "title": "calc_var_x",
          "type": ["number","string"],
          "examples": [
            "chromatisity_white_x",
            30.5
          ],
          "default": 0
        },
        "calc_var_y": {
          "$id": "#root/items/calc_var_y",
          "title": "calc_var_y",
          "type": ["number","string"],
          "examples": [
            30.5
          ],
          "default": 0
        },
        "calc_var_z": {
          "$id": "#root/items/calc_var_z",
          "title": "calc_var_z",
          "type": ["number","string"],
          "examples": [
            30.5
          ],
          "default": 0
        }
      }
    }
  }
}