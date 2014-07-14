<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    version="1.0">
    <xsl:template match="root">
        <xsl:variable name="origin_x">70</xsl:variable>
        <xsl:variable name="origin_y">230</xsl:variable>
        <xsl:variable name="height" select="$origin_y - 15"/>
        <xsl:variable name="width" select="600"/>
        <xsl:variable name="count" select="count(object)"/>
        <xsl:variable name="bar_margin" select="10"/>
        <xsl:variable name="bar_width" select="($width - (($count + 1)*$bar_margin)) div $count"/>
        <xsl:variable name="y_axis_text_offset" select="22"/>
        
        <svg class="bar_chart" height="300px" width="500px" viewBox="0 0 720 250" xmlns="http://www.w3.org/2000/svg">
            <line stroke="darkgrey" x1="{$origin_x}" y1="{$origin_y - $height*.25}" x2="{$origin_x+$width}" y2="{$origin_y - $height*.25}"/>
            <line stroke="darkgrey" x1="{$origin_x}" y1="{$origin_y - $height*.5}" x2="{$origin_x+$width}" y2="{$origin_y - $height*.5}"/>
            <line stroke="darkgrey" x1="{$origin_x}" y1="{$origin_y - $height*.75}" x2="{$origin_x+$width}" y2="{$origin_y - $height*.75}"/>
            <text style="font-family:sans-serif;font-size:12px" x="{$origin_x - $y_axis_text_offset}" y="{$origin_y - $height*.25}">.25</text>
            <text style="font-family:sans-serif;font-size:12px" x="{$origin_x - $y_axis_text_offset}" y="{$origin_y - $height*.5}">.50</text>
            <text style="font-family:sans-serif;font-size:12px" x="{$origin_x - $y_axis_text_offset}" y="{$origin_y - $height*.75}">.75</text>
            <text style="font-family:sans-serif;font-size:12px" x="{$origin_x - 1.5*$y_axis_text_offset}" y="{$origin_y - $height*.15}" transform="rotate(-90 {$origin_x - 1.5*$y_axis_text_offset},{$origin_y - $height*.15})">Frequency of Correct Answers</text>
            
            <linearGradient id="grad1" x1="0%" x2="0%" y1="0%" y2="100%">
                <stop offset="0%" style="stop-color:deepskyblue;stop-opacity:1"/>
                <stop offset="100%" style="stop-color:#98F5FF;stop-opacity:1"/>
            </linearGradient>
            <linearGradient id="grad2" x1="0%" x2="100%" y1="0%" y2="100%">
                <stop offset="0%" style="stop-color:ghostwhite;stop-opacity:1"/>
                <stop offset="100%" style="stop-color:lightgrey;stop-opacity:1"/>
            </linearGradient>
            <linearGradient id="grad3" x1="0%" x2="0%" y1="0%" y2="100%">
                <stop offset="0%" style="stop-color:black;stop-opacity:1"/>
                <stop offset="100%" style="stop-color:#98F5FF;stop-opacity:1"/>
            </linearGradient>
            
            <xsl:for-each select="object">
                <xsl:variable name="preceding_count" select="count(current()/preceding-sibling::object)"/>
                <xsl:if test="number(@fake) = 1">
                    <xsl:variable name="cx" select="$origin_x + ($preceding_count+1)*$bar_margin + $preceding_count*$bar_width + ($bar_width div 2)"/>
                    <xsl:variable name="cy" select="$origin_y - 30"></xsl:variable>
                    <circle cx="{$cx}" cy="{$cy}" r="{$bar_width div 3}" fill="url(#grad2)"></circle>
                </xsl:if>
                <xsl:if test="not(number(@fake) = 1)">
                    <xsl:variable name="bar_x" select="$origin_x + (($preceding_count+1)*$bar_margin)+ ($preceding_count*$bar_width)"/>
                    <xsl:variable name="bar_height" select="$height*@ratio"/>
                    <xsl:if test="number(@ratio) = 0">
                        <rect data-identifier="{@identifier}" stroke="none" rx="1" ry="1" stroke-width="1" fill="url(#grad1)" x="{$bar_x}" y="{$origin_y - 5}" width="{$bar_width}" height="{5}">
                            <title><xsl:value-of select="current()"/></title>
                        </rect>
                    </xsl:if>
                    <xsl:if test="not(number(@ratio) = 0)">
                        <rect data-identifier="{@identifier}" stroke="none" rx="1" ry="1" stroke-width="1" fill="url(#grad1)" x="{$bar_x}" y="{$origin_y - $bar_height}" width="{$bar_width}" height="{$bar_height}">
                            <title><xsl:value-of select="current()"/></title>
                        </rect>
                    </xsl:if>
                </xsl:if>
            </xsl:for-each>
            
            <line stroke="black" x1="{$origin_x}" y1="{$origin_y}" x2="{$origin_x}" y2="{$origin_y - $height}"/>
            <line stroke="black" x1="{$origin_x}" y1="{$origin_y}" x2="{$origin_x + $width}" y2="{$origin_y}"/>
            <text style="overflow:visible" font-size="10px" x="{$origin_x + 10}" y="{$origin_y + 15}" width="100" height="100">Most frequently answered</text>
            <text style="overflow:visible" font-size="10px" x="{$origin_x + $width - 128}" y="{$origin_y + 15}" width="100" height="100">Least frequently answered</text>
        </svg>
    </xsl:template>
</xsl:stylesheet>