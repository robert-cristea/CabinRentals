import React from "react";
import ReactDOM from "react-dom";
import "react-calendar-timeline/lib/Timeline.css";
import "./app.scss";
import App from "./app";
import "core-js/stable";
import "regenerator-runtime/runtime";

// require("dotenv").config();

const render = (AppToRender) => {
  ReactDOM.render(<AppToRender />, document.getElementById("root"));
};

render(App);

if (module.hot) {
  module.hot.accept("./app", () => {
    const NextApp = require("./app").default;

    render(NextApp);
  });
}
