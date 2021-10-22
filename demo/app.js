/* eslint-disable no-console */
import React, { useEffect, useState } from "react";
import moment from "moment";
import faker from "faker";

import Timeline, {
  TimelineMarkers,
  TodayMarker,
  CustomMarker,
  CursorMarker,
  SidebarHeader,
  CustomHeader,
  TimelineHeaders,
  DateHeader,
} from "react-calendar-timeline";

import generateFakeData from "./generate-fake-data";
import axios from "axios";

var minTime = moment()
  .add(-6, "months")
  .valueOf();
var maxTime = moment()
  .add(6, "months")
  .valueOf();

var keys = {
  groupIdKey: "id",
  groupTitleKey: "title",
  groupRightTitleKey: "rightTitle",
  itemIdKey: "id",
  itemTitleKey: "title",
  itemDivTitleKey: "title",
  itemGroupKey: "group",
  itemTimeStartKey: "start",
  itemTimeEndKey: "end",
};

const API_URL = "http://localhost";

export default function App() {
  const [groups, setGroups] = useState([]);
  const [items, setItems] = useState([]);
  const [defaultTimeStart, setDefaultTimeStart] = useState(null);
  const [defaultTimeEnd, setDefaultTimeEnd] = useState(null);
  const [format, setFormat] = useState(false);
  const [showHeaders, setShowHeaders] = useState(false);

  useEffect(() => {
    const { groups, items } = generateFakeData();

    console.log("generateFakeData", groups, items);
    setGroups(groups);
    setItems(items);
    const defaultTimeStart = moment()
      .startOf("day")
      .toDate();
    const defaultTimeEnd = moment()
      .startOf("day")
      .add(1, "day")
      .toDate();
    setDefaultTimeStart(defaultTimeStart);
    setDefaultTimeEnd(defaultTimeEnd);
  }, []);

  useEffect(() => {
    fetchRentals();
    fetchReservation();
  }, []);

  const fetchRentals = async () => {
    try {
      const { data: rentals } = await axios.get(
        `${API_URL}/cabinrentals/api/get-rentals.php`
      );
      console.log(`fetchRentals:data`, rentals);
      let groups = rentals.map((rental) => ({
        id: rental.PropertyID,
        title: rental.PropertyTitle,
        label: `Label ${rental.PropertyID}`,
        // bgColor: randomColor({ luminosity: "light", seed: randomSeed + i }),
      }));
      setGroups(groups);
    } catch (error) {
      console.log(`fetchRentals:error`, error);
    }
  };

  function generateCheckinDatetime(date) {
    // moment(date).add(16, "h");
    // console.log(`generateCheckinDatetime`, moment(date).add(16, "h"));
    return moment(date).add(16, "h");
  }

  function generateCheckoutDatetime(date) {
    // moment(date).add(9, "h");
    return moment(date).add(9, "h");
  }

  const fetchReservation = async () => {
    try {
      const { data: reservations } = await axios.get(
        `${API_URL}/cabinrentals/api/get-reservations.php`
      );
      console.log(`fetchReservation:data`, reservations);
      const daysInPast = 30;
      const startDate =
        faker.date.recent(daysInPast).valueOf() +
        daysInPast * 0.3 * 86400 * 1000;
      const startValue =
        Math.floor(moment(startDate).valueOf() / 10000000) * 10000000;
      const endValue = moment(
        startDate + faker.random.number({ min: 2, max: 20 }) * 15 * 60 * 1000
      ).valueOf();

      console.log(`startDate`, startDate, startValue, endValue);

      let items = reservations.map((reservation, index) => ({
        id: index + "",
        group: reservation.PropertyID,
        title: reservation.CabinName,
        start: generateCheckinDatetime(reservation.StartDate),
        end: generateCheckoutDatetime(reservation.EndDate),
        itemProps: {
          "data-tip": reservation.CabinName,
        },
      }));
      setItems(items);
    } catch (error) {
      console.log(`fetchReservation:error`, error);
    }
  };

  const handleClick = () => {
    setFormat(true);
  };

  const handleCanvasClick = (groupId, time) => {
    console.log("Canvas clicked", groupId, moment(time).format());
  };

  const handleCanvasDoubleClick = (groupId, time) => {
    console.log("Canvas double clicked", groupId, moment(time).format());
  };

  const handleCanvasContextMenu = (group, time) => {
    console.log("Canvas context menu", group, moment(time).format());
  };

  const handleItemClick = (itemId, _, time) => {
    console.log("Clicked: " + itemId, moment(time).format());
  };

  const handleItemSelect = (itemId, _, time) => {
    console.log("Selected: " + itemId, moment(time).format());
  };

  const handleItemDoubleClick = (itemId, _, time) => {
    console.log("Double Click: " + itemId, moment(time).format());
  };

  const handleItemContextMenu = (itemId, _, time) => {
    console.log("Context Menu: " + itemId, moment(time).format());
  };

  const handleItemMove = (itemId, dragTime, newGroupOrder) => {
    const group = groups[newGroupOrder];
    setItems(
      items.map((item) =>
        item.id === itemId
          ? Object.assign({}, item, {
              start: dragTime,
              end: dragTime + (item.end - item.start),
              group: group.id,
            })
          : item
      )
    );

    console.log("Moved", itemId, dragTime, newGroupOrder);
  };

  const handleItemResize = (itemId, time, edge) => {
    setItems(
      items.map((item) =>
        item.id === itemId
          ? Object.assign({}, item, {
              start: edge === "left" ? time : item.start,
              end: edge === "left" ? item.end : time,
            })
          : item
      )
    );

    console.log("Resized", itemId, time, edge);
  };

  // this limits the timeline to -6 months ... +6 months
  const handleTimeChange = (
    visibleTimeStart,
    visibleTimeEnd,
    updateScrollCanvas
  ) => {
    if (visibleTimeStart < minTime && visibleTimeEnd > maxTime) {
      updateScrollCanvas(minTime, maxTime);
    } else if (visibleTimeStart < minTime) {
      updateScrollCanvas(
        minTime,
        minTime + (visibleTimeEnd - visibleTimeStart)
      );
    } else if (visibleTimeEnd > maxTime) {
      updateScrollCanvas(
        maxTime - (visibleTimeEnd - visibleTimeStart),
        maxTime
      );
    } else {
      updateScrollCanvas(visibleTimeStart, visibleTimeEnd);
    }
  };

  const moveResizeValidator = (action, item, time) => {
    if (time < new Date().getTime()) {
      var newTime =
        Math.ceil(new Date().getTime() / (15 * 60 * 1000)) * (15 * 60 * 1000);
      return newTime;
    }

    return time;
  };

  const handleClickChangeHeaders = () => {
    setShowHeaders(!showHeaders);
  };

  if (!defaultTimeStart || !defaultTimeEnd) return null;

  return (
    <div>
      <button onClick={handleClick}>format</button>
      <button onClick={handleClickChangeHeaders}>add headers</button>
      <Timeline
        groups={groups}
        items={items}
        keys={keys}
        sidebarWidth={150}
        sidebarContent={<div>Above The Left</div>}
        canMove
        canResize="right"
        canSelect
        itemsSorted
        itemTouchSendsClick={false}
        stackItems
        itemHeightRatio={0.75}
        defaultTimeStart={defaultTimeStart}
        defaultTimeEnd={defaultTimeEnd}
        onCanvasClick={handleCanvasClick}
        onCanvasDoubleClick={handleCanvasDoubleClick}
        onCanvasContextMenu={handleCanvasContextMenu}
        onItemClick={handleItemClick}
        onItemSelect={handleItemSelect}
        onItemContextMenu={handleItemContextMenu}
        onItemMove={handleItemMove}
        onItemResize={handleItemResize}
        onItemDoubleClick={handleItemDoubleClick}
        onTimeChange={handleTimeChange}
        // moveResizeValidator={moveResizeValidator}
      >
        <TimelineHeaders className="header-background">
          <SidebarHeader />
          <DateHeader
            labelFormat={format ? "d" : undefined}
            unit="primaryHeader"
          />
          <DateHeader height={50} />
          <CustomHeader unit="year" headerData={{ hey: "you" }}>
            {({
              headerContext: { intervals },
              getRootProps,
              getIntervalProps,
              showPeriod,
              data,
            }) => {
              console.log("props", data);
              return (
                <div {...getRootProps()}>
                  {intervals.map((interval) => {
                    const intervalStyle = {
                      lineHeight: "30px",
                      textAlign: "center",
                      borderLeft: "1px solid black",
                      cursor: "pointer",
                      backgroundColor: "Turquoise",
                      color: "white",
                    };
                    return (
                      <div
                        onClick={() => {
                          showPeriod(interval.startTime, interval.endTime);
                        }}
                        {...getIntervalProps({
                          interval,
                          style: intervalStyle,
                        })}
                      >
                        <div className="sticky">
                          {interval.startTime.format("YYYY")}
                        </div>
                      </div>
                    );
                  })}
                </div>
              );
            }}
          </CustomHeader>
          <CustomHeader unit="week">
            {({
              headerContext: { intervals },
              getRootProps,
              getIntervalProps,
              showPeriod,
            }) => {
              return (
                <div {...getRootProps()}>
                  {intervals.map((interval) => {
                    const intervalStyle = {
                      lineHeight: "30px",
                      textAlign: "center",
                      borderLeft: "1px solid black",
                      cursor: "pointer",
                      backgroundColor: "indianred",
                      color: "white",
                    };
                    return (
                      <div
                        onClick={() => {
                          showPeriod(interval.startTime, interval.endTime);
                        }}
                        {...getIntervalProps({
                          interval,
                          style: intervalStyle,
                        })}
                      >
                        <div className="sticky">
                          {interval.startTime.format("MM/DD")}
                        </div>
                      </div>
                    );
                  })}
                </div>
              );
            }}
          </CustomHeader>
          <CustomHeader>
            {({
              headerContext: { intervals },
              getRootProps,
              getIntervalProps,
              showPeriod,
            }) => {
              return (
                <div {...getRootProps()}>
                  {intervals.map((interval) => {
                    const intervalStyle = {
                      lineHeight: "30px",
                      textAlign: "center",
                      borderLeft: "1px solid black",
                      cursor: "pointer",
                    };
                    return (
                      <div
                        onClick={() => {
                          showPeriod(interval.startTime, interval.endTime);
                        }}
                        {...getIntervalProps({
                          interval,
                          style: intervalStyle,
                        })}
                      >
                        {interval.startTime.format("HH")}
                      </div>
                    );
                  })}
                </div>
              );
            }}
          </CustomHeader>
          <DateHeader
            unit="week"
            labelFormat="MM/DD"
            height={50}
            headerData={{ hey: "date header" }}
            intervalRenderer={({ getIntervalProps, intervalContext, data }) => {
              console.log("intervalRenderer props", data);
              return (
                <div {...getIntervalProps()}>
                  {intervalContext.intervalText}
                </div>
              );
            }}
          />
          {showHeaders
            ? [
                <DateHeader
                  labelFormat={format ? "d" : undefined}
                  unit="primaryHeader"
                />,
                <DateHeader height={50} />,
              ]
            : null}
        </TimelineHeaders>
        <TimelineMarkers>
          <TodayMarker />
          <CustomMarker
            date={
              moment()
                .startOf("day")
                .valueOf() +
              1000 * 60 * 60 * 2
            }
          />
          <CustomMarker
            date={moment()
              .add(3, "day")
              .valueOf()}
          >
            {({ styles }) => {
              const newStyles = { ...styles, backgroundColor: "blue" };
              return <div style={newStyles} />;
            }}
          </CustomMarker>
          <CursorMarker />
        </TimelineMarkers>
      </Timeline>
    </div>
  );
}
