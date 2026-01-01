import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { ChevronDownIcon } from 'lucide-react';
import type { Dispatch, SetStateAction } from 'react';
import { useState } from 'react';

interface CalendarDatePickerProps {
    value: Date | null;
    setValue: Dispatch<SetStateAction<Date | null>>;
}

export default function CalendarDatePicker({ value, setValue }: Readonly<CalendarDatePickerProps>) {
    const [open, setOpen] = useState(false);
    const today = new Date();
    const currentYear = today.getFullYear();
    const minDate = new Date(currentYear, 0, 1); // Jan 1 of current year
    const maxDate = new Date(currentYear + 5, 11, 31); // Dec 31 five years from current year

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button variant="outline" id="date" className="w-48 justify-between font-normal">
                    {value ? value.toLocaleDateString() : 'Select date'}
                    <ChevronDownIcon aria-hidden />
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-auto overflow-hidden p-0" align="start">
                <Calendar
                    mode="single"
                    selected={value ?? undefined}
                    captionLayout="dropdown"
                    showOutsideDays={false}
                    startMonth={new Date(currentYear, 0)}
                    endMonth={new Date(currentYear + 5, 11)}
                    disabled={(date) => date < minDate || date > maxDate}
                    onSelect={(date) => {
                        setValue(date ?? null);
                        setOpen(false);
                    }}
                />
            </PopoverContent>
        </Popover>
    );
}
