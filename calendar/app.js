import React, { useState, useEffect } from "react";
import Timelineview from "./timelineview";
import useWindowDimensions from "./useWindowDimensions";

export default function App() {
  const { height, width } = useWindowDimensions();

  return <Timelineview isMobile={width < 700} />;
}
