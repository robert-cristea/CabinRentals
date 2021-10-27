/* eslint-disable no-console */
import React, {useEffect, useState} from "react";
import Container from "react-bootstrap/Container";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import {useLocation} from "react-router-dom";
import moment from "moment";
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
import faker from "faker";
import generateFakeData from "./generate-fake-data";
import axios from "axios";
import {FaCalendar, FaChevronLeft, FaChevronRight} from "react-icons/fa";

let minTime = moment()
    .add(-6, "months")
    .valueOf();
let maxTime = moment()
    .add(6, "months")
    .valueOf();

let keys = {
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

const API_URL = process.env.API_URL || "http://localhost/cabinrentals";

console.log('process.env.API_URL', process.env.API_URL)

export default function Timelineview({isMobile}) {
    const [groups, setGroups] = useState([]);
    const [items, setItems] = useState([]);
    const [defaultTimeStart, setDefaultTimeStart] = useState(null);
    const [defaultTimeEnd, setDefaultTimeEnd] = useState(null);
    const [visibleTimeStart, setVisibleTimeStart] = useState(null);
    const [visibleTimeEnd, setVisibleTimeEnd] = useState(null);
    const [format, setFormat] = useState(false);
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        console.log(`isMobile`, isMobile);
        let startValue, endValue;
        startValue = moment().startOf("day");
        if (isMobile) {
            endValue = moment()
                .startOf("day")
                .add(1, "week");
        } else {
            endValue = moment()
                .startOf("day")
                .add(1, "month");
        }
        setDefaultTimeStart(startValue);
        setDefaultTimeEnd(endValue);
        setVisibleTimeStart(startValue);
        setVisibleTimeEnd(endValue);
        fetchRentals();
        fetchReservation();
    }, [isMobile]);

    // useEffect(() => {
    //   const params = new URLSearchParams(window.location.search);
    //   let defaultTimeStart = params.get("start");
    //   let defaultTimeEnd = params.get("end");
    //   console.log(`location`, defaultTimeStart, defaultTimeEnd);

    //   // fetchReservation();
    // }, [window.location]);

    const fetchRentals = async () => {
        setIsLoading(true);
        try {
            const {data: rentals} = await axios.get(
                `${API_URL}/api/get-rentals.php`
            );
            console.log(`fetchRentals:data`, rentals);
            let groups = rentals.map((rental) => ({
                id: rental.PropertyID,
                title: rental.PropertyTitle,
                label: `Label ${rental.PropertyID}`,
            }));
            setGroups(groups);
        } catch (error) {
            console.log(`fetchRentals:error`, error);
        }
        setIsLoading(false);
    };

    const fetchReservation = async (startDate, endDate) => {
        setIsLoading(true);
        try {
            if (!startDate || !endDate) {
                startDate = moment()
                    .startOf("day")
                    .add(-1, "month")
                    .format();
                endDate = moment()
                    .startOf("day")
                    .add(2, "month")
                    .format();
            } else {
                startDate = startDate.startOf("day").format();
                endDate = endDate.startOf("day").format();
            }
            console.log(`fetchReservation`, startDate, endDate);
            const {data: reservations} = await axios.get(
                `${API_URL}/api/get-reservations.php?start=${startDate}&end=${endDate}`
            );
            console.log(`fetchReservation:data`, reservations);

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
            console.log(`fetchReservation:items`, items);
            setItems(items);
        } catch (error) {
            console.log(`fetchReservation:error`, error);
        }
        setIsLoading(false);
    };

    const generateCheckinDatetime = (date) => {
        // return (
        //   Math.floor(
        //     moment(date)
        //       .add(16, "h")
        //       .valueOf() / 10000000
        //   ) * 10000000
        // );
        return moment(date).add(16, "h");
    };

    const generateCheckoutDatetime = (date) => {
        return moment(date).add(9, "h");
    };

    const handleLeftClick = () => {
        let newVisibleTimeStart = moment(visibleTimeStart).add(-1, "month");
        console.log(`handleLeftClick:newVisibleTimeStart`, newVisibleTimeStart);

        let startDate = moment(newVisibleTimeStart).add(-1, "month");
        let endDate = moment(newVisibleTimeStart).add(2, "month");

        setDefaultTimeStart(moment(newVisibleTimeStart));
        setDefaultTimeEnd(
            moment(newVisibleTimeStart)
                .startOf("day")
                .add(1, "month")
        );

        setVisibleTimeStart(moment(newVisibleTimeStart));
        setVisibleTimeEnd(
            moment(newVisibleTimeStart)
                .startOf("day")
                .add(1, "month")
        );
        console.log(`handleLeftClick:startDate, endDate`, startDate, endDate);

        fetchReservation(startDate, endDate);
    };

    const handleRightClick = () => {
        let newVisibleTimeStart = moment(visibleTimeStart).add(1, "month");
        console.log(`handleRightClick:newVisibleTimeStart`, newVisibleTimeStart);

        let startDate = moment(newVisibleTimeStart).add(-1, "month");
        let endDate = moment(newVisibleTimeStart).add(2, "month");

        setDefaultTimeStart(moment(newVisibleTimeStart));
        setDefaultTimeEnd(
            moment(newVisibleTimeStart)
                .startOf("day")
                .add(1, "month")
        );

        setVisibleTimeStart(moment(newVisibleTimeStart));
        setVisibleTimeEnd(
            moment(newVisibleTimeStart)
                .startOf("day")
                .add(1, "month")
        );
        console.log(`handleRightClick:startDate, endDate`, startDate, endDate);

        fetchReservation(startDate, endDate);
    };

    // this limits the timeline to -6 months ... +6 months
    const handleTimeChange = (
        visibleTimeStart,
        visibleTimeEnd,
        updateScrollCanvas
    ) => {
        console.log(
            `handleTimeChange:visibleTimeStart,visibleTimeEnd`,
            visibleTimeStart,
            visibleTimeEnd,
            moment(visibleTimeStart),
            moment(visibleTimeEnd)
        );

        /**
         * scroll
         */
        // setVisibleTimeStart(visibleTimeStart);
        // setVisibleTimeEnd(visibleTimeEnd);

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

    const handleBoundChange = (canvasTimeStart, canvasTimeEnd) => {
        console.log(
            `handleBoundChange`,
            canvasTimeStart,
            canvasTimeEnd,
            moment(canvasTimeEnd)
        );
        fetchReservation(
            moment(canvasTimeStart).format(),
            moment(canvasTimeEnd).format()
        );
    };

    const itemRenderer = ({
                              item,
                              timelineContext,
                              itemContext,
                              getItemProps,
                              getResizeProps,
                          }) => {
        const {left: leftResizeProps, right: rightResizeProps} = getResizeProps();
        return (
            <div {...getItemProps()}>
                {itemContext.useResizeHandle ? <div {...leftResizeProps} /> : null}

                <div
                    style={{
                        height: itemContext.dimensions.height,
                        overflow: "hidden",
                        paddingLeft: 3,
                        textOverflow: "ellipsis",
                        whiteSpace: "nowrap",
                    }}
                >
                    <a target="_blank" href={`${API_URL}/rental.php?id=${item.group}`}>
                        {itemContext.title}
                    </a>
                </div>

                {itemContext.useResizeHandle ? <div {...rightResizeProps} /> : null}
            </div>
        );
    };

    if (!defaultTimeStart || !defaultTimeEnd) return null;
    if (items.length === 0 || groups.length === 0) return null;

    return (
        <Container>
            <div className="calendar">
                <div className="calendar__action">
                    <div className="calendar__navigator">
                        <div onClick={handleLeftClick}>
                            <FaChevronLeft/>
                        </div>
                        <div>
                            <FaCalendar/>
                        </div>
                        <div onClick={handleRightClick}>
                            <FaChevronRight/>
                        </div>
                    </div>
                    <div className="calendar__range">
            <span>
              {moment(visibleTimeStart)
                  .startOf("day")
                  .format("Y-MM-DD")}
            </span>
                        <span> ~ </span>
                        <span>
              {moment(visibleTimeEnd)
                  .startOf("day")
                  .format("Y-MM-DD")}
            </span>
                    </div>
                </div>
                <Timeline
                    groups={groups}
                    items={items}
                    keys={keys}
                    sidebarWidth={isMobile ? 50 : 150}
                    sidebarContent={<div>Above The Left</div>}
                    canMove={false}
                    canResize="right"
                    canSelect
                    itemsSorted
                    itemTouchSendsClick={false}
                    stackItems
                    lineHeight={40}
                    itemHeightRatio={0.75}
                    itemRenderer={itemRenderer}
                    defaultTimeStart={defaultTimeStart}
                    defaultTimeEnd={defaultTimeEnd}
                    visibleTimeStart={moment(visibleTimeStart).valueOf()}
                    visibleTimeEnd={moment(visibleTimeEnd).valueOf()}
                    onTimeChange={handleTimeChange}
                    onBoundsChange={handleBoundChange}
                >
                    <TimelineHeaders className="header-background">
                        <SidebarHeader/>
                        <DateHeader
                            labelFormat={format ? "d" : undefined}
                            unit="primaryHeader"
                        />
                        {/* <DateHeader height={50} /> */}
                        <DateHeader
                            unit="day"
                            labelFormat="MMM/DD"
                            height={80}
                            headerData={{data: "date header"}}
                            intervalRenderer={({
                                                   getIntervalProps,
                                                   intervalContext,
                                                   data,
                                               }) => {
                                // console.log("intervalRenderer props", intervalContext, data);
                                return (
                                    <div
                                        {...getIntervalProps()}
                                        className="rct-calendar-header__cell"
                                    >
                    <span>
                      {moment(intervalContext.interval.startTime).format("ddd")}
                    </span>
                                        <br/>
                                        <span>{intervalContext.intervalText.split("/")[0]}</span>
                                        <br/>
                                        <span>{intervalContext.intervalText.split("/")[1]}</span>
                                    </div>
                                );
                            }}
                        />
                    </TimelineHeaders>
                    <TimelineMarkers>
                        <TodayMarker/>
                        <CustomMarker
                            date={
                                moment()
                                    .startOf("day")
                                    .valueOf() +
                                1000 * 60 * 60 * 2
                            }
                        />
                        {/* <CustomMarker
            date={moment()
              .add(3, "day")
              .valueOf()}
          >
            {({ styles }) => {
              const newStyles = { ...styles, backgroundColor: "blue" };
              return <div style={newStyles} />;
            }}
          </CustomMarker> */}
                        <CursorMarker/>
                    </TimelineMarkers>
                </Timeline>
            </div>
        </Container>
    );
}
